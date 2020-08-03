import React, { useState } from 'react';
import Avatar from '@material-ui/core/Avatar';
import Button from '@material-ui/core/Button';
import Checkbox from '@material-ui/core/Checkbox';
import Container from '@material-ui/core/Container';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Grid from '@material-ui/core/Grid';
import Link from '@material-ui/core/Link';
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';

import ReCAPTCHA from 'react-google-recaptcha';

import Footer from '../components/Footer';
import RouterLink from '../components/RouterLink';
import { useAuthContext } from '../context/Auth';
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

export default function Login(props) {
    const classes = useStyles();
    const { loginUser } = useAuthContext();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const [data, setData] = useState({});
    const recaptchaRef = React.useRef();

    const handleLogin = e => {
        setError(null);
        setIsLoading(true);
        e.preventDefault();

        // Fetch recaptcha token before logging in
        recaptchaRef.current.executeAsync().then(token => {
            let promise = loginUser(data.email, data.password, data.remember_me ? 1 : 0, token);
            promise
                .then(() => {
                    props.history.push('/dashboard');
                })
                .catch(error => {
                    setIsLoading(false);

                    // Reset Captcha
                    recaptchaRef.current.reset();

                    switch(error.response.status){
                    case 401:
                        setError('Votre connexion a échoué. Veuillez vérifier vos identifiants et réessayer.');
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
                <Avatar className={classes.avatar}>
                    <LockOutlinedIcon />
                </Avatar>
                <Typography component="h1" variant="h5">
                    Authentification requise
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
                        onChange={e => { setData({ ...data, email: e.target.value }); }}
                        autoComplete="email"
                        autoFocus
                    />
                    <TextField
                        variant="outlined"
                        margin="normal"
                        required
                        fullWidth
                        type="password"
                        id="password"
                        name="password"
                        label="Mot de Passe"
                        onChange={e => { setData({ ...data, password: e.target.value }); }}
                        autoComplete="current-password"
                    />
                    <FormControlLabel
                        control={
                            <Checkbox
                                value="remember"
                                color="primary"
                                onChange={e => { setData({ ...data, remember_me: e.target.checked }); }}
                            />
                        }
                        label="Se souvenir de moi"
                    />
                    {!!error && (
                        <Typography component="p" variant="body1" align="center" color="error" gutterBottom>
                            {error}
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
                            disabled={isLoading}
                            className={classes.submit}
                        >
                        Connexion
                        </Button>
                        {isLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                    </div>
                    <Grid container>
                        <Grid item xs>
                            <Link component={RouterLink} to="/forgotpassword">
                                <Typography component="div" variant="body2" align="center" color="textPrimary">
                                    {'Mot de passe oublié?'}
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