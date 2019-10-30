import React, { createContext, useMemo, useState, useEffect, useContext } from 'react';

export const AuthContext = createContext(null);

const initialAuthData = {};

const AuthProvider = props => {
    const [authData, setAuthData] = useState(initialAuthData);

    useEffect(() => {
        const state = localStorage["appState"];
        if (state) {
            let appState = JSON.parse(state);
            setAuthData({ user: appState.user });
            console.log(authData);
        }
    }, []); // Empty array means useEffect will only be called on first render

    const loginUser = (email, password) => {
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
                    // alert("Login Successful!");

                    let user = {
                        id: json.data.data.id,
                        first_name: json.data.data.first_name,
                        last_name: json.data.data.last_name,
                        email: json.data.data.email,
                        auth_token: json.data.data.auth_token,
                        timestamp: new Date().toString()
                    };
                    setAuthData({ user });
                    localStorage["appState"] = JSON.stringify({ user });
                } else {
                    // alert("Login Failed!");
                }
                return json;
            })
            .catch(error => {
                alert(`An Error Occured! ${error}`);
            });
    };

    const logoutUser = () => {
        setAuthData(initialAuthData);
        localStorage["appState"] = JSON.stringify({ user: null });
    };

    // Memoize given object as long as authData does not change
    const authDataValue = useMemo(() => ({ ...authData, loginUser, logoutUser }), [authData]);

    return <AuthContext.Provider value={authDataValue} {...props} />;
};

export const useAuthContext = () => useContext(AuthContext);

export default AuthProvider;
