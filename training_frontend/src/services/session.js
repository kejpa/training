import { globalUrl } from "../components/App/App";

const url = globalUrl() + "/sessions/";

export function addSession(item, user) {
    return fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Access-Control-Request-Method": "POST",
            "Access-Control-Request-Headers": "user-token, Content-Type",
            "user-token": user.token,
        },
        body: JSON.stringify(item),
    });
}

export function updateSession(item, user) {
    return fetch(url + item.id, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "Access-Control-Request-Method": "POST",
            "Access-Control-Request-Headers": "user-token, Content-Type",
            "user-token": user.token,
        },
        body: JSON.stringify(item),
    }).then((data) => data.json());
}

export function deleteSession(item, user) {
    return fetch(url + item.id, {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "Access-Control-Request-Method": "POST",
            "Access-Control-Request-Headers": "user-token, Content-Type",
            "user-token": user.token,
        },
        body: JSON.stringify(item),
    }).then((data) => data.json());
}

export function getSessions(user) {
    return fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "Access-Control-Request-Method": "GET",
            "Access-Control-Request-Headers": "user-token, Content-Type",
            "user-token": user.token,
        },
    });
}

export function getSession(id) {
    return fetch(url + id).then((data) => data.json());
}
