import { useEffect, useState } from "react";
import { getSessions } from "../../services/session";
import Moment from "moment";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";
import { Bar } from "react-chartjs-2";
import Loading from "../Loading/Loading";
import useUser from "../App/useUser";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

export const options = {
    responsive: true,
    plugins: {
        legend: false,
        title: {
            display: true,
            text: "Simmade sträckor månadsvis",
        },
    },
};

export default function Graph() {
    const [data, setData] = useState();
    const [loading, setLoading] = useState(true);
    const user = useUser().user;

    useEffect(() => {
        let mounted = true;
        getSessions(user)
            .then(async (response) => {
                if (response.ok) {
                    if (mounted) {
                        let items = await response.json();
                        let sessions = items.sessions;
                        sessions = sessions.sort((a, b) => {
                            let first = new Date(a.date);
                            let second = new Date(b.date);
                            return first - second;
                        });
                        let arr = sessions.reduce((acc, itm) => {
                            let month = Moment(itm.date).format("YYYY-MM");
                            acc[month] ??= 0;
                            acc[month] += parseInt(itm.length);
                            return acc;
                        }, []);
                        let res = [];
                        for (const m in arr) {
                            res.push({ month: m, total: arr[m] });
                        }
                        setData({
                            labels: res.map((itm) => itm.month),
                            datasets: [
                                {
                                    label: "Dataset 1",
                                    data: res.map((itm) => itm.total),
                                    backgroundColor: "darkcyan",
                                },
                            ],
                        });
                    }
                }
            })
            .finally(() => {
                setLoading(false);
            });
    }, [user]);

    return (
        <div className="wrapper">
            {!loading ? <Bar options={options} data={data} /> : <Loading />}
        </div>
    );
}
