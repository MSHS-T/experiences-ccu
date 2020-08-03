import React, { useState } from 'react';
import Button from '@material-ui/core/Button';
import Container from '@material-ui/core/Container';
import Grid from '@material-ui/core/Grid';
import Link from '@material-ui/core/Link';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';

import ReCAPTCHA from 'react-google-recaptcha';

import * as Constants from '../data/Constants';

import Footer from '../components/Footer';
import RouterLink from '../components/RouterLink';
import { CircularProgress } from '@material-ui/core';
import { green } from '@material-ui/core/colors';

const useStyles = makeStyles(theme => ({
    '@global': {
        body: {
            backgroundColor: theme.palette.common.white,
        },
    },
    paper: {
        marginTop:     theme.spacing(8),
        display:       'flex',
        flexDirection: 'column',
        alignItems:    'center',
    },
    avatar: {
        margin:          theme.spacing(1),
        backgroundColor: theme.palette.secondary.main,
    },
    form: {
        width:     '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(1),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
    successText: {
        color: theme.palette.success.main
    },
    buttonWrapper: {
        margin:         theme.spacing(2),
        position:       'relative',
        display:        'flex',
        flexDirection:  'column',
        justifyContent: 'center'
    },
    buttonProgress: {
        color:      green[500],
        position:   'absolute',
        top:        '50%',
        left:       '50%',
        marginTop:  -12,
        marginLeft: -12,
    },
}));

export default function ForgotPassword() {
    const classes = useStyles();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);
    const [data, setData] = useState({});
    const recaptchaRef = React.useRef();

    const handleLogin = e => {
        setError(null);
        setIsLoading(true);
        e.preventDefault();

        // Fetch recaptcha token before logging in
        recaptchaRef.current.executeAsync().then(token => {
            // Build form data
            var formData = new FormData();
            formData.append('email', data);
            formData.append('g-recaptcha-response', token || '');

            // eslint-disable-next-line no-undef
            axios.post(Constants.API_URL + 'auth/forgotpassword', formData)
                .then(() => {
                    setIsLoading(false);
                    setSuccess(true);
                })
                .catch(error => {
                    setIsLoading(false);
                    // Reset Captcha
                    recaptchaRef.current.reset();

                    switch(error.response.status){
                    case 404:
                        setError('Cette adresse e-mail est inconnue. Veuillez vérifier votre saisie et réessayer.');
                        break;
                    case 400:
                        setError('Le système anti-robots a rejeté votre action. Veuillez actualiser la page et réessayer.');
                        break;
                    default:
                        console.error(JSON.stringify(error));
                        setError('Erreur inconnue. Veuillez actualiser la page et réessayer. Si le problème persiste, merci de contacter un administrateur.');
                        break;
                    }
                });
        });

    };

    return (
        <Container component="main" maxWidth="xs">
            <div className={classes.paper}>
                <Link component={RouterLink} to="/">
                    <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                        Expériences CCU
                    </Typography>
                </Link>
                <Typography component="h1" variant="h5" align="center" gutterBottom>
                    {'Mot de passe oublié ?'}
                </Typography>
                <Typography component="h2" variant="h6" align="center" gutterBottom>
                    {'Saisissez l\'adresse email de votre compte afin de réinitialiser votre mot de passe'}
                </Typography>
                <form className={classes.form} noValidate onSubmit={handleLogin}>
                    <TextField
                        variant="outlined"
                        margin="normal"
                        required
                        fullWidth
                        id="email"
                        name="email"
                        label="Adresse E-Mail"
                        onChange={e => { setData(e.target.value); }}
                        autoComplete="email"
                        autoFocus
                    />
                    {!!error && (
                        <Typography component="p" variant="body1" align="center" color="error" gutterBottom>
                            {error}
                        </Typography>
                    )}
                    {!!success && (
                        <Typography component="p" variant="body1" align="center" className={classes.successText} gutterBottom>
                            {'Un lien de réinitialisation vient d\'être envoyé à votre adresse mail.'}
                        </Typography>
                    )}
                    <ReCAPTCHA
                        ref={recaptchaRef}
                        size="invisible"
                        theme="dark"
                        // eslint-disable-next-line no-undef
                        sitekey={RECAPTCHA_KEY}
                    />
                    <div className={classes.buttonWrapper}>
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                            disabled={isLoading || success}
                            className={classes.submit}
                        >
                        Réinitialiser
                        </Button>
                        {isLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                    </div>
                    <Grid container>
                        <Grid item xs>
                            <Link component={RouterLink} to="/login">
                                <Typography component="div" variant="body2" align="center" color="textPrimary">
                                    {'Connexion'}
                                </Typography>
                            </Link>
                        </Grid>
                    </Grid>
                </form>
            </div>
            <Footer />
        </Container>
    );
}