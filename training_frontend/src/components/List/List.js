import { useEffect, useState } from "react";
import { getSessions } from "../../services/session";
import useUser from "../App/useUser";
import Loading from "../Loading/Loading";
import "./List.css";

export default function List() {
    const [sessions, setSessions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [status, setStatus] = useState();
    const user = useUser().user;

    useEffect(() => {
        let mounted = true;
        getSessions(user)
            .then(async (response) => {
                if (response.ok) {
                    if (mounted) {
                        let s = await response.json();
                        setSessions(s.sessions);
                    }
                } else {
                    let err = await response.json();
                    setStatus(err.join("<br />"));
                }
            })
            .finally(() => {
                setLoading(false);
            });
    }, [user]);

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Sträcka</th>
                        <th>Beskrivning</th>
                        <th>rpe</th>
                    </tr>
                </thead>
                <tbody>
                    {sessions.map((item) => (
                        <tr key={item.id}>
                            <td>{item.date}</td>
                            <td>{item.length}</td>
                            <td className="description">{item.description}</td>
                            <td className={"rpe" + item.rpe}>{item.rpe}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <p id="status">{status}</p>
        </div>
    );
}
