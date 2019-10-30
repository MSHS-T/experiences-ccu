import React from "react";
import { Route, Switch } from "react-router-dom";
import { useAuthContext } from "../context/Auth";
import LandingPage from "../pages/Landing";
import LoginPage from "../pages/Login";
import DashboardPage from "../pages/Dashboard";
import UsersPage from "../pages/Users";

const PrivateRoute = ({ component, ...options }) => {
    const { user } = useAuthContext();
    const finalComponent = user ? component : LoginPage;

    return <Route {...options} component={finalComponent} />;
};

const Router = () => (
    <Switch>
        <Route exact path="/" component={LandingPage} />
        <Route path="/login" component={LoginPage} />

        <PrivateRoute path="/dashboard" component={DashboardPage} />
        <PrivateRoute path="/users" component={UsersPage} />
    </Switch>
);

export default Router;