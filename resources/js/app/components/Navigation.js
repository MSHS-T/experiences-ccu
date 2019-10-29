import React, { useContext, useState } from 'react';
import { Redirect } from 'react-router-dom';

import { makeStyles, useTheme } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Button from '@material-ui/core/Button';
import Divider from '@material-ui/core/Divider';
import Drawer from '@material-ui/core/Drawer';
import Hidden from '@material-ui/core/Hidden';
import Link from '@material-ui/core/Link';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import MenuIcon from '@material-ui/icons/Menu';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';

import BuildIcon from '@material-ui/icons/Build';
import DashboardIcon from '@material-ui/icons/Dashboard';
import HomeIcon from '@material-ui/icons/Home';
import MeetingRoomIcon from '@material-ui/icons/MeetingRoom';
import PeopleIcon from '@material-ui/icons/People';
import SettingsIcon from '@material-ui/icons/Settings';
import ShutterSpeedIcon from '@material-ui/icons/ShutterSpeed';

import IconButton from '@material-ui/core/IconButton';

import RouterLink from './RouterLink';
import UserContext from '../context/User';

export default function Navigation({ children }) {
    const user = useContext(UserContext);
    const drawerWidth = user.isLoggedIn ? 200 : 0;

    const useStyles = makeStyles(theme => ({
        root: {
            paddingTop: theme.mixins.toolbar.minHeight,
            [theme.breakpoints.up('md')]: {
                paddingLeft: drawerWidth,
            },
        },
        appBar: {
            borderBottom: `1px solid ${theme.palette.divider}`,
            zIndex: theme.zIndex.drawer + 1,
        },
        drawer: {
            width: drawerWidth,
            flexShrink: 0,
        },
        toolbar: theme.mixins.toolbar,
        menuButton: {
            marginRight: theme.spacing(2),
            [theme.breakpoints.up('md')]: {
                display: 'none',
            },
        },
        toolbarTitle: {
            flexGrow: 1,
        },
        drawerPaper: {
            width: drawerWidth,
        },
        button: {
            margin: theme.spacing(1, 1.5),
        },
    }));

    const classes = useStyles();
    const theme = useTheme();
    const [redirect, setRedirect] = useState(null);

    const [mobileOpen, setMobileOpen] = useState(false);

    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };

    const handleLogout = e => {
        e.preventDefault();

        user.logoutUser();
        setRedirect('/');
    };

    const navLinks = [
        { title: 'Accueil', icon: (<HomeIcon />), url: '/' },
        { title: 'Dashboard', icon: (<DashboardIcon />), url: '/dashboard' },
        'divider',
        { title: 'Plateaux', icon: (<MeetingRoomIcon />), url: '/plateaux' },
        { title: 'Manipulations', icon: (<ShutterSpeedIcon />), url: '/manipulations' },
        { title: 'Matériel', icon: (<BuildIcon />), url: '/equipment' },
        'divider',
        { title: 'Utilisateurs', icon: (<PeopleIcon />), url: '/users' },
        { title: 'Paramètres', icon: (<SettingsIcon />), url: '/settings' },
        'divider',
    ]

    const drawer = (
        <div>
            <div className={classes.toolbar} />
            <Divider />
            <List>
                {navLinks.map((link, index) => {
                    if (link === "divider") { return (<Divider key={index} />) }
                    return (
                        <ListItem button component={RouterLink} to={link.url} key={index}>
                            <ListItemIcon>{link.icon}</ListItemIcon>
                            <ListItemText primary={link.title} />
                        </ListItem>
                    )
                })}
            </List>
        </div>
    );

    return (
        <div className={classes.root}>
            {redirect !== null ? <Redirect to={redirect} /> : ""}
            <AppBar position="fixed" color="default" elevation={0} className={classes.appBar}>
                <Toolbar className={classes.toolbar}>
                    {
                        user.isLoggedIn ?
                            (
                                <IconButton
                                    edge="start"
                                    className={classes.menuButton}
                                    color="inherit"
                                    aria-label="menu"
                                    onClick={handleDrawerToggle}
                                >
                                    <MenuIcon />
                                </IconButton>
                            ) : ''
                    }
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
                                        onClick={handleLogout}
                                        to="/logout"
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
            {
                user.isLoggedIn ?
                    (
                        <>
                            {/* The implementation can be swapped with js to avoid SEO duplication of links. */}
                            <Hidden mdUp implementation="css">
                                <Drawer
                                    variant="temporary"
                                    anchor={theme.direction === 'rtl' ? 'right' : 'left'}
                                    open={mobileOpen}
                                    onClose={handleDrawerToggle}
                                    classes={{
                                        paper: classes.drawerPaper,
                                    }}
                                    ModalProps={{
                                        keepMounted: true, // Better open performance on mobile.
                                    }}
                                >
                                    {drawer}
                                </Drawer>
                            </Hidden>
                            <Hidden smDown implementation="css">
                                <Drawer
                                    classes={{
                                        paper: classes.drawerPaper,
                                    }}
                                    variant="permanent"
                                    open
                                >
                                    {drawer}
                                </Drawer>
                            </Hidden>
                        </>
                    ) : ''
            }
            {children}
        </div>
    );
}