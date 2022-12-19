import { useState } from "react";
import "./Login.css";
import PropTypes from "prop-types";
import { globalUrl } from "../App/App";

export default function Login({ setToken }) {
    const [username, setUsername] = useState();
    const [password, setPassword] = useState();
    const [error, setError] = useState("");

    async function loginUser(credentials) {
        console.log(globalUrl());
        const data = await fetch(globalUrl() + "/login/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(credentials),
        });
        return data;
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        loginUser({ username, password }).then(async (response) => {
            if (response.ok) {
                setToken(await response.json());
            } else {
                const err = await response.json();
                setError(err.message.join("<br />"));
                document.getElementById("loginFailed").style = "display:block;";
            }
        });
    };

    return (
        <div className="login-wrapper">
            <h1>Logga in!</h1>
            <form onSubmit={handleSubmit}>
                <label>
                    <p>Användare</p>
                    <input
                        type="text"
                        onChange={(e) => setUsername(e.target.value)}
                    />
                </label>
                <label>
                    <p>Lösen</p>
                    <input
                        type="password"
                        onChange={(e) => setPassword(e.target.value)}
                    />
                </label>
                <div>
                    <button type="submit">Logga in</button>
                </div>
            </form>
            <p id="loginFailed" dangerouslySetInnerHTML={{ __html: error }} />
        </div>
    );
}

Login.propTypes = {
    setToken: PropTypes.func.isRequired,
};
