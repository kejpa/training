const express = require("express");
const cors = require("cors");
const app = express();

app.use(cors());

app.use("/login/", (req, res) => {
    res.send({
        user: {
            id: 1,
            email: "kjell@kejpa.com",
            firstname: "Kjell",
            lastname: "Hansen",
            token: "229164564c5513d057f2",
            tokenDate: "2022-11-11 18:24",
        },
    });
});

app.use("/checkToken/", (req, res) => {
    let data = { token: 1234 };

    if (data.token === 1234) {
        setTimeout(() => {
            res.send({
                user: {
                    id: 1,
                    email: "kjell@kejpa.com",
                    firstname: "Kjell",
                    lastname: "Hansen",
                    token: "229164564c5513d057f2",
                    tokenDate: "2022-11-11 18:24",
                },
            });
        }, 1500);
    } else {
        setTimeout(() => {
            res.status(401).send({
                message: ["Bad token", "User credientials not validated"],
            });
        }, 1500);
    }
});

app.listen(8080, () =>
    console.log("Login API running on http://localhost:8080")
);
