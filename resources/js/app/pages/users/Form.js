import React, { Component, useState, useEffect } from "react";

import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import FormControl from '@material-ui/core/FormControl';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormLabel from '@material-ui/core/FormLabel';
import Checkbox from '@material-ui/core/Checkbox';
import Grid from '@material-ui/core/Grid';
import Typography from "@material-ui/core/Typography";
import { makeStyles } from '@material-ui/core/styles';

import CancelIcon from '@material-ui/icons/Cancel';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from "../../context/Auth";
import ErrorPage from "../Error";
import Loading from "../Loading";

const useStyles = makeStyles(theme => ({
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    buttonRow: {
        display: 'flex',
        justifyContent: 'center'
    },
    button: {
        margin: theme.spacing(2),
    },
}));

const allRoles = {
    ADMIN: "Administrateur",
    PLAT: "Responsable Plateau",
    MANIP: "Responsable Manipulation",
}

export default function UserForm(props) {
    const classes = useStyles();
    const { user, accessToken } = useAuthContext();

    const [mode, setMode] = useState("CREATE");
    const [isLoading, setLoading] = useState(false);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);

    const loadData = (id) => {
        setLoading(true);
        setUserData([]);

        fetch('http://localhost/api/user/' + id, { headers: { 'Authorization': 'bearer ' + accessToken } })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json()
            })
            // Set data in state
            .then(data => {
                console.log(data);
                setUserData(data);
                setError(null);
                setLoading(false);
            })
            .catch(err => {
                setUserData(null);
                setError(err.message);
                setLoading(false);
            });
    }

    useEffect(() => {
        if (props.match.params.hasOwnProperty("id")) {
            setMode("EDIT");
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

    if (isLoading) {
        return <Loading />
    }
    if (error !== null || (mode === "EDIT" && userData === null)) {
        console.debug(error);
        return (
            <ErrorPage>
                Une erreur s'est produite : <strong>{(error !== null ? error : ("No data"))}</strong>
            </ErrorPage>
        )
    }

    let _first_name, _last_name, _email, _password, _roles = {};

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {mode === "CREATE" ? "Nouvel Utilisateur" : `Modification de l'utilisateur #${props.match.params.id}`}
            </Typography>
            <hr />
            <form className={classes.form} noValidate>
                <Grid container spacing={2}>
                    <Grid item xs={12} sm={6}>
                        <TextField
                            id="first_name"
                            name="first_name"
                            label="Prénom"
                            autoComplete="fname"
                            inputRef={input => (_first_name = input)}
                            value={mode === "EDIT" ? userData.first_name : ""}
                            variant="outlined"
                            required
                            fullWidth
                            autoFocus
                        />
                    </Grid>
                    <Grid item xs={12} sm={6}>
                        <TextField
                            id="last_name"
                            name="last_name"
                            label="Nom"
                            autoComplete="lname"
                            inputRef={input => (_last_name = input)}
                            value={mode === "EDIT" ? userData.last_name : ""}
                            variant="outlined"
                            required
                            fullWidth
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <TextField
                            id="email"
                            name="email"
                            label="Adresse Email"
                            autoComplete="email"
                            inputRef={input => (_email = input)}
                            value={mode === "EDIT" ? userData.email : ""}
                            variant="outlined"
                            required
                            fullWidth
                        />
                    </Grid>
                    {mode === "EDIT" ? "" : (
                        <Grid item xs={12}>
                            <TextField
                                id="password"
                                name="password"
                                label="Mot de passe initial"
                                inputRef={input => (_password = input)}
                                variant="outlined"
                                required
                                fullWidth
                            />
                        </Grid>
                    )}
                    <Grid item xs={12}>
                        <FormControl component="fieldset">
                            <FormLabel component="legend">
                                Rôles :
                        </FormLabel>
                            <FormGroup>
                                {Object.entries(allRoles).map(r => (
                                    <FormControlLabel
                                        key={r[0]}
                                        label={r[1]}
                                        control={<Checkbox
                                            value={r[0]}
                                            id={"roles-" + r[0]}
                                            names="roles"
                                            inputRef={input => console.log(input)}
                                            checked={mode === "EDIT" && userData.roles.map(r => r.key).includes(r[0])}
                                            color="primary"
                                        />}
                                    />
                                ))}
                            </FormGroup>
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} className={classes.buttonRow}>
                        <Button
                            variant="contained"
                            color="secondary"
                            className={classes.button}
                            startIcon={<CancelIcon />}
                            onClick={e => props.history.push('/users')}
                        >
                            Annuler
                        </Button>
                        <Button
                            type="submit"
                            variant="contained"
                            color="primary"
                            size="large"
                            className={classes.button}
                            startIcon={<SaveIcon />}
                        >
                            {mode === "CREATE" ? "Créer" : "Modifier"}
                        </Button>
                    </Grid>
                </Grid>
            </form>
        </>
    )
}

