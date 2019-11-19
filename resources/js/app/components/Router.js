import React from "react";
import { Route, Switch } from "react-router-dom";
import intersection from 'lodash/intersection';

import { useAuthContext } from "../context/Auth";
import SiteMap from '../data/SiteMap';

import LoginPage from "../pages/Login";
import ErrorPage from "../pages/Error";

const PrivateRoute = ({ component, roles, ...options }) => {
    // Fetch user
    const { user } = useAuthContext();

    // If user is empty (anonymous user), we will render the login page
    let finalComponent = LoginPage;

    if (user) {
        // Else we render the given component
        finalComponent = component;
        // We check if there is at least 1 common role between the user's and the requirements
        const isAllowed = intersection(user.roles, roles).length > 0;
        if (!isAllowed) {
            // If no matching roles are found, we render the Error page
            return <Route render={(props) => (
                <ErrorPage {...props}>
                    Vous n'êtes pas autorisé à accéder à cette page.
                    <br />
                    Niveau d'accès requis : {roles.join(' ou ')}
                </ErrorPage>
            )} {...options} />
        }
    }

    return <Route {...options} component={finalComponent} />;
};

const Router = () => (
    <Switch>
        {/* We filter the dividers out */}
        {SiteMap.filter(link => link !== '---').map((link, index) => {
            const RouteType = link.authenticated ? PrivateRoute : Route;
            return (
                <RouteType
                    key={index}
                    path={link.url}
                    exact={link.exactPath}
                    roles={link.roles}
                    component={link.component}
                />
            )
        })}
    </Switch>
);

export default Router;