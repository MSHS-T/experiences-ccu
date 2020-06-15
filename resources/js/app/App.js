import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { CssBaseline } from '@material-ui/core';
import { ConfirmProvider } from 'material-ui-confirm';

import * as moment from 'moment';

import Navigation from './components/Navigation';
import Router from './components/Router';

import AuthProvider from './context/Auth';


class App extends Component {
    constructor(props) {
        super(props);
        moment.updateLocale('fr', { week: {
            dow: 1, // First day of week is Monday
            doy: 4  // First week of year must contain 4 January (7 + 1 - 4)
        }});
        moment.locale('fr');
    }
    render() {
        return (
            <>
                <Helmet
                    titleTemplate="%s | Expériences CCU"
                >
                    <meta charset="utf-8" />
                    <meta name="viewport" content="minimum-scale=1, initial-scale=1, width=device-width, shrink-to-fit=no" />
                    <title>Accueil</title>
                </Helmet>
                <BrowserRouter>
                    <AuthProvider>
                        <ConfirmProvider defaultOptions={{
                            title:            'Êtes-vous sûr ?',
                            confirmationText: 'Confirmer',
                            cancellationText: 'Annuler'
                        }}>
                            <Navigation>
                                <CssBaseline />
                                <Router />
                            </Navigation>
                        </ConfirmProvider>
                    </AuthProvider>
                </BrowserRouter>
            </>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('app'));