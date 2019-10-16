import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route } from 'react-router-dom';
import { CssBaseline } from '@material-ui/core';
import { createMuiTheme } from '@material-ui/core/styles';

import LandingPage from './pages/Landing';
import LoginPage from './pages/Login';
import DashboardPage from './pages/Dashboard';

import { UserProvider } from './context/User';

// createMuiTheme();

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <UserProvider>
                <BrowserRouter>
                    <>
                        <CssBaseline />
                        <Route exact path="/" component={LandingPage} />
                        <Route path="/login" component={LoginPage} />
                        <Route path="/dashboard" component={DashboardPage} />
                    </>
                </BrowserRouter>
            </UserProvider>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));