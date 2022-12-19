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
                if(response.ok) {
                if (mounted) {
                    let s = await response.json();
                    setSessions(s.sessions);
                }} else {
                    let err=await response.json();
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
                    </tr>
                </thead>
                <tbody>
                    {sessions.map((item) => (
                        <tr key={item.id}>
                            <td>{item.date}</td>
                            <td>{item.length}</td>
                            <td
                                dangerouslySetInnerHTML={{
                                    __html: item.description.replace(
                                        "\n",
                                        "<br />"
                                    ),
                                }}
                            />
                        </tr>
                    ))}
                </tbody>
            </table>
            <p id="status">{status}</p>
        </div>
    );
}
