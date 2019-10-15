import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route } from 'react-router-dom';

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
    return <div>Dashboard Page</div>;
};

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <BrowserRouter>
                <div>
                    <Route exact path="/" component={LandingPage} />
                    <Route path="/login" component={LoginPage} />
                    <Route path="/register" component={RegisterPage} />
                    <Route path="/dashboard" component={DashboardPage} />
                </div>
            </BrowserRouter>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));