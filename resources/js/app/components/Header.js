import React, { useContext } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Button from '@material-ui/core/Button';
import IconButton from '@material-ui/core/IconButton';
import Link from '@material-ui/core/Link';
import MenuIcon from '@material-ui/icons/Menu';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';

import RouterLink from './RouterLink';
import UserContext from '../context/User';

const useStyles = makeStyles(theme => ({
    appBar: {
        borderBottom: `1px solid ${theme.palette.divider}`,
    },
    toolbar: {
        flexWrap: 'wrap',
    },
    menuButton: {
        marginRight: theme.spacing(2),
    },
    toolbarTitle: {
        flexGrow: 1,
    },
    button: {
        margin: theme.spacing(1, 1.5),
    },
}));



export default function Header() {
    const classes = useStyles();
    const user = useContext(UserContext);

    return (
        <>
            <AppBar position="static" color="default" elevation={0} className={classes.appBar}>
                <Toolbar className={classes.toolbar}>
                    <IconButton edge="start" className={classes.menuButton} color="inherit" aria-label="menu">
                        <MenuIcon />
                    </IconButton>
                    <Link component={RouterLink} to="/" color="inherit" className={classes.toolbarTitle}>
                        <Typography variant="h6" color="inherit" noWrap>
                            Expériences CCU
                        </Typography>
                    </Link>
                    <>
                        {
                            user.isLoggedIn ?
                                (
                                    <Button
                                        component={RouterLink}
                                        to="/login"
                                        color="inherit"
                                        variant="outlined"
                                        className={classes.button}
                                    >
                                        Logout
                                    </Button>
                                ) :
                                (
                                    <Button
                                        component={RouterLink}
                                        to="/login"
                                        color="inherit"
                                        variant="outlined"
                                        className={classes.button}
                                    >
                                        Login
                                    </Button>
                                )
                        }

                    </>
                </Toolbar>
            </AppBar>
        </>
    );
}