import { useState } from "react";
import { createUseStyles } from "react-jss";
import { Link } from "react-router-dom";

const useStyles = createUseStyles({
    wrapper: {
        display: "flex",
        padding: [0, 5],
    },
    tab: {
        padding: [0, 5, 0, 5],
        backgroundColor: "darkcyan",
        border: "solid 1px lightblue",
        flexGrow: 1,
        textAlign: "center",
        "& a": {
            textDecoration: "none",
        },
        "& img": {
            height: 40,
        },
    },
    active: {
        backgroundColor: "lightblue",
        borderBottom: "none",
    },
});

export default function Tabs({ tabs, selectedIndex }) {
    const style = useStyles();
    const [activeTab, setActiveTab] = useState(selectedIndex);

    return (
        <>
            <nav className={style.wrapper}>
                {tabs.map((tab) => (
                    <span
                        key={tab.index}
                        className={[
                            style.tab,
                            activeTab === tab.index ? style.active : "",
                        ].join(" ")}
                    >
                        <Link
                            to={tab.view}
                            onClick={() => {
                                setActiveTab(tab.index);
                            }}
                        >
                            <img src={tab.image} alt="" />
                        </Link>
                    </span>
                ))}
            </nav>
        </>
    );
}
