import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route } from 'react-router-dom';
import { createMuiTheme } from '@material-ui/core/styles';

import LandingPage from './pages/Landing';
import LoginPage from './pages/Login';
import DashboardPage from './pages/Dashboard';

const theme = createMuiTheme();

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
                    <Route path="/dashboard" component={DashboardPage} />
                </>
            </BrowserRouter>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));