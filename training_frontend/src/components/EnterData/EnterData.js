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
            rpe:""
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
        rpe:""
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
                />{" "}
                m
            </label>
            <label>Beskrivning</label>
            <textarea
                name="description"
                onChange={handleChange}
                value={formData.description}
            />
            <br />
            <label>
                Rpe:{" "}
                <select name="rpe" value={formData.rpe} onChange={handleChange}>
                    <option value={""} className="rpe">
                        Inget
                    </option>
                    <option value={1} className="rpe1">
                        1
                    </option>
                    <option value={2} className="rpe2">
                        2
                    </option>
                    <option value={3} className="rpe3">
                        3
                    </option>
                    <option value={4} className="rpe4">
                        4
                    </option>
                    <option value={5} className="rpe5">
                        5
                    </option>
                    <option value={6} className="rpe6">
                        6
                    </option>
                    <option value={7} className="rpe7">
                        7
                    </option>
                    <option value={8} className="rpe8">
                        8
                    </option>
                    <option value={9} className="rpe9">
                        9
                    </option>
                    <option value={10} className="rpe10">
                        10
                    </option>
                </select>
            </label>
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
