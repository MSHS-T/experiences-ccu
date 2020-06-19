import React, { useState, useEffect } from 'react';
import { withRouter } from 'react-router-dom';

import { makeStyles } from '@material-ui/core/styles';
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
import { Avatar, MenuItem, Menu, createMuiTheme, ThemeProvider } from '@material-ui/core';
import PersonIcon from '@material-ui/icons/Person';

function Navigation(props) {
    const { user, logoutUser } = useAuthContext();
    const drawerWidth = user ? 200 : 0;

    const classes = makeStyles(theme => ({
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
            marginRight:                  theme.spacing(0.5),
            [theme.breakpoints.up('md')]: {
                display: 'none',
            },
        },
        toolbarDivider: {
            flexGrow: 1,
        },
        drawerPaper: {
            width: drawerWidth,
        },
        avatar: {
            border: `1px solid ${theme.palette.divider}`,
            margin: theme.spacing(0, 3),
            cursor: 'pointer'
        },
        button: {
            margin: theme.spacing(1, 1.5),
        },
    }))();


    // Dark Mode management
    const [isDarkMode, setIsDarkMode] = useState(false);
    const darkTheme = createMuiTheme({
        palette: {
            type: isDarkMode ? 'dark' : 'light'
        }
    });
    const toggleTheme = () => {
        if(isDarkMode){
            setIsDarkMode(false);
            window.localStorage.setItem('theme', 'light');
        } else {
            setIsDarkMode(true);
            window.localStorage.setItem('theme', 'dark');
        }
    };
    useEffect(() => {
        const localTheme = window.localStorage.getItem('theme');
        !!localTheme && setIsDarkMode(localTheme === 'dark');
    }, []);

    // Drawer open state
    const [mobileOpen, setMobileOpen] = useState(false);

    // User menu
    const [anchorEl, setAnchorEl] = useState(null);
    const userMenuOpen = Boolean(anchorEl);

    const handleUserMenu = event => {
        setAnchorEl(event.currentTarget);
    };

    const handleUserMenuClose = () => {
        setAnchorEl(null);
    };


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
        <ThemeProvider theme={darkTheme}>
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
                        <img src={`/favicon${isDarkMode ? '-dark-mode' : ''}.png`} alt="Logo" height="22" />
                    &nbsp;
                        <Link component={RouterLink} to="/" color="inherit" underline="none">
                            <Typography component="h1" variant="h6" color="inherit" noWrap>
                            Expériences CCU
                            </Typography>
                        </Link>
                        <div className={classes.toolbarDivider}> </div>
                        <>
                            {!!user && (
                                <Avatar
                                    aria-label="account of current user"
                                    aria-controls="menu-appbar"
                                    aria-haspopup="true"
                                    onClick={handleUserMenu}
                                    color="inherit"
                                    className={classes.avatar}
                                >
                                    <PersonIcon />
                                </Avatar>
                            )}
                            {!!user && (
                                <Menu
                                    id="menu-appbar"
                                    anchorEl={anchorEl}
                                    anchorOrigin={{
                                        vertical:   'bottom',
                                        horizontal: 'right',
                                    }}
                                    keepMounted
                                    transformOrigin={{
                                        vertical:   'top',
                                        horizontal: 'right',
                                    }}
                                    open={userMenuOpen}
                                    onClose={handleUserMenuClose}
                                    getContentAnchorEl={null}
                                >
                                    <MenuItem
                                        onClick={handleUserMenuClose}
                                        component={RouterLink}
                                        to="/profile"
                                    >
                                        Profil
                                    </MenuItem>
                                    <MenuItem
                                        onClick={toggleTheme}
                                    >
                                        {isDarkMode ? 'Thème Clair' : 'Thème sombre'}
                                    </MenuItem>
                                    <Divider />
                                    <MenuItem
                                        onClick={handleLogout}
                                    >
                                        Déconnexion
                                    </MenuItem>
                                </Menu>
                            )}
                            {!user && (
                                <Button
                                    component={RouterLink}
                                    to="/login"
                                    color="inherit"
                                    variant="outlined"
                                    className={classes.button}
                                >
                            Connexion
                                </Button>
                            )}
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
                                        anchor="left"
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
        </ThemeProvider>
    );
}

export default withRouter(Navigation);