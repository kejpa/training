import { useState } from "react";

export default function useUser() {
    const getUser = () => {
        const userString = localStorage.getItem("user");
        const user = JSON.parse(userString);
        return user ?? {};
    };
    const saveUser = (userToken) => {
        if (userToken === null) {
            localStorage.removeItem("user");
            setUser(null);
        } else {
            localStorage.setItem("user", JSON.stringify(userToken.user));
            setUser(userToken.user);
        }
    };

    const [user, setUser] = useState(getUser());

    return { setUser: saveUser, user };
}
