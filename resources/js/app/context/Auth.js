import React, { createContext, useMemo, useState, useEffect, useContext } from 'react';
import * as Constants from "../data/Constants";

export const AuthContext = createContext(null);

const initialAuthData = {};
const initialAccessToken = "";

const AuthProvider = props => {
    const [authData, setAuthData] = useState(initialAuthData);
    const [accessToken, setAccessToken] = useState(initialAccessToken);

    useEffect(() => {
        // Check if we have a stored state
        const state = localStorage["appState"];
        if (state) {
            let appState = JSON.parse(state);
            // Check if data is expired firs
            if (appState.tokenExpiration < Date.now()) {
                // If it is, clear user data, we are logged out
                clearUserData();
            } else {
                // If it is not, store user data and access token in state
                setAuthData({ user: appState.user });
                setAccessToken(appState.token);
            }

        }
    }, []); // Empty array means useEffect will only be called on first render

    const loginUser = (email, password, remember_me) => {
        // Build form data
        var formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);
        formData.append("remember_me", remember_me);

        // Send login query
        return axios
            .post(Constants.API_URL + "auth/login", formData)
            .then(json_token => {
                // Store access token
                setAccessToken(json_token.data.access_token);

                // Send /me query to get user information
                axios.post(Constants.API_URL + "auth/me", {}, {
                    headers: { 'Authorization': "bearer " + json_token.data.access_token }
                }).then(json_me => {
                    let user = {
                        id: json_me.data.id,
                        first_name: json_me.data.first_name,
                        last_name: json_me.data.last_name,
                        email: json_me.data.email,
                        auth_token: json_me.data.auth_token,
                        timestamp: new Date().toString(),
                        roles: json_me.data.roles.map(r => r.key),
                    };
                    // Store user data in state
                    setAuthData({ user });
                    // Store user data, token and expiration date in local storage
                    localStorage["appState"] = JSON.stringify({
                        user,
                        token: json_token.data.access_token,
                        tokenExpiration: json_token.data.expires_at * 1000
                    });
                });
            })
            .catch(error => {
                // TODO : Fail gracefully
                alert(`An Error Occured! ${error}`);
            });
    };

    const logoutUser = () => {
        // Send logout query
        return axios.post(Constants.API_URL + "auth/logout", {}, {
            headers: { 'Authorization': "bearer " + accessToken }
        }).then(json => {
            // Once it's done, clear user data
            clearUserData();
        });
    };

    const clearUserData = () => {
        // Remove auth data
        setAuthData(initialAuthData);
        // Remove auth token
        setAccessToken(initialAccessToken);
        // Clear local storage
        localStorage.clear();
    }

    // Memoize given object as long as authData does not change
    const authDataValue = useMemo(() => ({ ...authData, accessToken, loginUser, logoutUser }), [authData]);

    return <AuthContext.Provider value={authDataValue} {...props} />;
};

export const useAuthContext = () => useContext(AuthContext);

export default AuthProvider;
