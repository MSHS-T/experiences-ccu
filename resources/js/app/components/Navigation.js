import React, { useState } from 'react';
import { withRouter } from 'react-router-dom';

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

import SiteMap from '../data/SiteMap';

import IconButton from '@material-ui/core/IconButton';

import RouterLink from './RouterLink';
import { useAuthContext } from '../context/Auth';

function Navigation(props) {
    const { user, logoutUser } = useAuthContext();
    const drawerWidth = user ? 200 : 0;

    const useStyles = makeStyles(theme => ({
        root: {
            paddingTop:                   theme.mixins.toolbar.minHeight + 20,
            paddingLeft:                  20,
            paddingRight:                 20,
            [theme.breakpoints.up('md')]: {
                paddingLeft: drawerWidth + 20,
            },
        },
        appBar: {
            borderBottom: `1px solid ${theme.palette.divider}`,
            zIndex:       theme.zIndex.drawer + 1,
        },
        drawer: {
            width:      drawerWidth,
            flexShrink: 0,
        },
        toolbar:    theme.mixins.toolbar,
        menuButton: {
            marginRight:                  theme.spacing(2),
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
    const [mobileOpen, setMobileOpen] = useState(false);

    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };

    const handleLogout = e => {
        e.preventDefault();

        let promise = logoutUser();
        promise.then(() => {
            props.history.push('/');
        });
    };

    const drawer = (
        <div>
            <div className={classes.toolbar} />
            <Divider />
            <List>
                {SiteMap.filter(link => (link === '---' || link.showInMenu)).map((link, index) => {
                    if (link === '---') { return (<Divider key={index} />); }
                    return (
                        <ListItem button component={RouterLink} to={link.url} key={index}>
                            <ListItemIcon>{link.icon}</ListItemIcon>
                            <ListItemText primary={link.title} />
                        </ListItem>
                    );
                })}
            </List>
        </div>
    );

    return (
        <div className={classes.root}>
            <AppBar position="fixed" color="default" elevation={0} className={classes.appBar}>
                <Toolbar className={classes.toolbar}>
                    {
                        user ?
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
                    <img src="/favicon.png" alt="Logo" height="22" />
                    &nbsp;
                    <Link component={RouterLink} to="/" color="inherit" underline="none" className={classes.toolbarTitle}>
                        <Typography component="h1" variant="h6" color="inherit" noWrap>
                            Expériences CCU
                        </Typography>
                    </Link>
                    <>
                        {/* TODO : Replace logout button with user menu */}
                        {
                            user ?
                                (
                                    <Button
                                        onClick={handleLogout}
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
                user ?
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
            {props.children}
        </div>
    );
}

export default withRouter(Navigation);