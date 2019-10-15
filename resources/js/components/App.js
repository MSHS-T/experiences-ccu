import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route } from 'react-router-dom';
import { createMuiTheme } from '@material-ui/core/styles';

import Header from './Header';

const theme = createMuiTheme();

const LandingPage = (props) => {
    return <div>Landing Page</div>;
};
const LoginPage = (props) => {
    return <div>Log In Page</div>;
};
const RegisterPage = (props) => {
    return <div>Register Page</div>;
};
const DashboardPage = (props) => {
    return (
        <div>
            <Header />
            Dashboard Page
        </div>
    );
};

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <BrowserRouter>
                <>
                    <Route exact path="/" component={LandingPage} />
                    <Route path="/login" component={LoginPage} />
                    <Route path="/register" component={RegisterPage} />
                    <Route path="/dashboard" component={DashboardPage} />
                </>
            </BrowserRouter>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));