import { useReducer, useState } from "react";
import Moment from "moment";
import "./EnterData.css";
import { addSession, updateSession } from "../../services/session";
import useUser from "../App/useUser";

const formReducer = (state, data) => {
    if (data.reset) {
        return {
            length: "",
            date: Moment().format("YYYY-MM-DD"),
            description: "",
        };
    }
    if (data.name === "length") {
        data.value = data.value.replace(/\D/g, "");
    }
    return { ...state, [data.name]: data.value };
};

export default function EnterData() {
    const user = useUser().user;
    const [submitting, setSubmitting] = useState(false);
    const [status, setStatus] = useState();
    const [formData, setFormData] = useReducer(formReducer, {
        length: "",
        date: Moment().format("YYYY-MM-DD"),
        description: "",
    });
    const handleChange = (event) => {
        setFormData({
            name: event.target.name,
            value: event.target.value,
        });
    };

    function handleSubmit(event) {
        event.preventDefault();
        if (formData.id === undefined && parseInt(formData.stracka) === 0) {
            console.log("No Submitting empty form");
            console.log(formData.id);
            console.log(formData.stracka);
            return false;
        }
        setSubmitting(true);
        if (!formData.id) {
            addSession(formData, user)
                .then(async (data) => {
                    if (data.ok) {
                        setStatus("Data sparad");
                        document.getElementById("status").className = "succe";
                        setFormData({ reset: true });
                    } else {
                        setStatus(await data.json());
                        document.getElementById("status").className = "error";
                    }
                })
                .finally(() => {
                    setSubmitting(false);
                    setTimeout(() => {
                        document.getElementById("status").className = "hidden";
                    }, 3000);
                });
        } else {
            updateSession(formData, user)
                .then((data) => {
                    setFormData({ reset: true });
                })
                .finally(() => {
                    setSubmitting(false);
                });
        }
    }

    return (
        <form onSubmit={handleSubmit}>
            <label>
                Datum:{" "}
                <input
                    type="date"
                    name="date"
                    onChange={handleChange}
                    value={formData.date}
                />
            </label>
            <label>
                Sträcka:{" "}
                <input
                    type="text"
                    name="length"
                    size="5"
                    value={formData.length}
                    onChange={handleChange}
                />Sträcka:{" "}
                m
            </label>
            <label>Beskrivning</label>
            <textarea
                name="description"
                onChange={handleChange}
                value={formData.description}
            />
            <br />
            <input type="hidden" name="id" value={formData.id} />
            <button type="submit" disabled={submitting}>
                Spara
            </button>{" "}
            <button
                type="reset"
                onClick={() => setFormData({ reset: true })}
                disabled={submitting}
            >
                Återställ
            </button>
            <p id="status">{status}</p>
        </form>
    );
}
