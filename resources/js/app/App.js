import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { CssBaseline } from '@material-ui/core';

import Navigation from './components/Navigation';
import Router from './components/Router';

import AuthProvider from './context/Auth';

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <BrowserRouter>
                <AuthProvider>
                    <Navigation>
                        <CssBaseline />
                        <Router />
                    </Navigation>
                </AuthProvider>
            </BrowserRouter>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));