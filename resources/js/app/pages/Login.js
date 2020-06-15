import React from 'react';
import Avatar from '@material-ui/core/Avatar';
import Box from '@material-ui/core/Box';
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

import Footer from '../components/Footer';
import RouterLink from '../components/RouterLink';
import { useAuthContext } from '../context/Auth';

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
}));

export default function Login(props) {
    const classes = useStyles();
    const { loginUser } = useAuthContext();
    let _email, _password, _remember;

    const handleLogin = e => {
        e.preventDefault();
        let promise = loginUser(_email.value, _password.value, _remember.checked ? 1 : 0);
        promise
            .then(() => {
                props.history.push('/dashboard');
            })
            .catch(error => {
                console.log(error);
                // TODO : Show error if login failed
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
                        inputRef={input => (_email = input)}
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
                        inputRef={input => (_password = input)}
                        autoComplete="current-password"
                    />
                    <FormControlLabel
                        control={<Checkbox value="remember" color="primary" inputRef={input => (_remember = input)} />}
                        label="Se souvenir de moi"
                    />
                    <Button
                        type="submit"
                        fullWidth
                        variant="contained"
                        color="primary"
                        className={classes.submit}
                    >
                        Connexion
                    </Button>
                    <Grid container>
                        <Grid item xs>
                            {/* TODO : Add link to forgot password page */}
                            <Link href="#" variant="body2">
                                {'Mot de passe oublié?'}
                            </Link>
                        </Grid>
                    </Grid>
                </form>
            </div>
            <Footer />
        </Container>
    );
}