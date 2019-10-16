import React, { useState, useEffect } from 'react';

const UserContext = React.createContext()

const UserProvider = (props) => {

    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [user, setUser] = useState(null);

    useEffect(() => {
        let state = localStorage["appState"];
        if (state) {
            let appState = JSON.parse(state);
            setIsLoggedIn(appState.isLoggedIn);
            setUser(appState.user);
        }
    }, []); // Empty array means useEffect will only be called on first render

    const loginUser = (email, password) => {
        console.log('login user');
        var formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        return axios
            .post("http://localhost/api/user/login/", formData)
            .then(response => {
                console.log(response);
                return response;
            })
            .then(json => {
                if (json.data.success) {
                    alert("Login Successful!");

                    let userData = {
                        id: json.data.data.id,
                        first_name: json.data.data.first_name,
                        last_name: json.data.data.last_name,
                        email: json.data.data.email,
                        auth_token: json.data.data.auth_token,
                        timestamp: new Date().toString()
                    };
                    setIsLoggedIn(true);
                    setUser(userData);
                    localStorage["appState"] = JSON.stringify({ isLoggedIn: true, user: userData });
                } else {
                    alert("Login Failed!");
                }
            })
            .catch(error => {
                alert(`An Error Occured! ${error}`);
            });
    };

    const logoutUser = () => {
        setIsLoggedIn(false);
        setUser(null);
        localStorage["appState"] = JSON.stringify({ isLoggedIn: false, user: null });
    };

    const context = {
        isLoggedIn,
        user,
        loginUser,
        logoutUser
    }

    return (
        <UserContext.Provider value={context}>
            {props.children}
        </UserContext.Provider>
    );
}

const UserConsumer = UserContext.Consumer
export { UserProvider, UserConsumer }
export default UserContext;
