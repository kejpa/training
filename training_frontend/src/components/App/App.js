import React, { useEffect, useState } from "react";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import EnterData from "../EnterData/EnterData";
import List from "../List/List";
import Graph from "../Graph/Graph";
import Login from "../Login/Login";
import Splash from "../Splash/Splash";
import Tabs from "../Tabs/Tabs";
import swim from "../Tabs/swim.png";
import list from "../Tabs/list.png";
import chart from "../Tabs/chart.png";
import "./App.css";
import useUser from "./useUser";

const tabs = [
    {
        index: 1,
        text: "Mata in",
        image: swim,
        view: "/enter",
    },
    {
        index: 2,
        text: "Lista",
        image: list,
        view: "/list",
    },
    {
        index: 3,
        text: "Diagram",
        image: chart,
        view: "/graph",
    },
];

function App() {
    const { user, setUser } = useUser();
    const [isLoading, setIsLoading] = useState(true);
    const [showLogin, setShowLogin] = useState(true);

    useEffect(() => {
        checkToken(user)
            .then((response) => {
                if (response.ok) {
                    return response.json();
                } else {
                    return null;
                }
            })
            .then((data) => {
                if (data === null) {
                    setIsLoading(false);
                    setShowLogin(true);
                    setUser(data);
                    return;
                }
                if (data.user.token !== user.token) {
                    setUser(data);
                }
                setIsLoading(false);
                setShowLogin(false);
            });
    }, [setUser, user]);

    if (isLoading) {
        return <Splash />;
    }
    if (showLogin) {
        return <Login setToken={setUser} />;
    }

    //    setShowLogin(false);
    let selectedIndex = 1;
    let href = tabs.find((t) => t.index === selectedIndex).view;
    if (window.location.pathname !== "/") {
        selectedIndex = tabs.find((t) => t.view === window.location.pathname)
            .index;
    }
    return (
        <BrowserRouter>
            <Tabs tabs={tabs} selectedIndex={selectedIndex} />
            <Routes>
                <Route path="/" element={<Navigate to={href} />} />
                <Route path="/enter" element={<EnterData />} />
                <Route path="/list" element={<List />} />
                <Route path="/graph" element={<Graph />} />
            </Routes>
        </BrowserRouter>
    );
}

export default App;
async function checkToken(userCredentials) {
    let data;
    userCredentials === null
        ? (data = await fetch(globalUrl() + "/checkToken/", {
              method: "POST",
              headers: {
                  "Access-Control-Request-Method": "POST",
                  "Access-Control-Request-Headers": "user-token, Content-Type",
                  "Content-Type": "application/json",
              },
          }))
        : (data = await fetch(globalUrl() + "/checkToken/", {
              method: "POST",
              headers: {
                  "Access-Control-Request-Method": "POST",
                  "Access-Control-Request-Headers": "user-token, Content-Type",
                  "Content-Type": "application/json",
                  "user-token": userCredentials.token,
              },
          }));
    return data;
}

export function globalUrl() {
    if (window.document.location.port === "3000") {
        return "http://api.localhost";
    } else {
        let locationArray = window.document.location.hostname.split(".");
        locationArray.shift();
        return (
            window.document.location.protocol +
            "//api." +
            locationArray.join(".")
        );
    }
}
