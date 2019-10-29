import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route } from 'react-router-dom';
import { CssBaseline } from '@material-ui/core';

import Navigation from './components/Navigation';

import LandingPage from './pages/Landing';
import LoginPage from './pages/Login';
import DashboardPage from './pages/Dashboard';
import UsersPage from './pages/Users';

import { UserProvider } from './context/User';

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <UserProvider>
                <BrowserRouter>
                    <Navigation>
                        <CssBaseline />
                        <Route exact path="/" component={LandingPage} />
                        <Route path="/login" component={LoginPage} />
                        <Route path="/dashboard" component={DashboardPage} />

                        <Route path="/users" component={UsersPage} />
                    </Navigation>
                </BrowserRouter>
            </UserProvider>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));