import React from 'react';

import BuildIcon from '@material-ui/icons/Build';
import DashboardIcon from '@material-ui/icons/Dashboard';
import EqualizerIcon from '@material-ui/icons/Equalizer';
import HomeIcon from '@material-ui/icons/Home';
import MeetingRoomIcon from '@material-ui/icons/MeetingRoom';
import PeopleIcon from '@material-ui/icons/People';
import SettingsIcon from '@material-ui/icons/Settings';
import ShutterSpeedIcon from '@material-ui/icons/ShutterSpeed';

import LandingPage from '../pages/Landing';
import LoginPage from '../pages/Login';
import ProfilePage from '../pages/Profile';
import ForgotPasswordPage from '../pages/ForgotPassword';
import ResetPasswordPage from '../pages/ResetPassword';
import DashboardPage from '../pages/Dashboard';

import UsersList from '../pages/users/List';
import UsersView from '../pages/users/View';
import UsersForm from '../pages/users/Form';

import EquipmentsList from '../pages/equipments/List';
import EquipmentsView from '../pages/equipments/View';
import EquipmentsForm from '../pages/equipments/Form';

import ManipulationsList from '../pages/manipulations/List';
import ManipulationsView from '../pages/manipulations/View';
import ManipulationsForm from '../pages/manipulations/Form';
import ManipulationsSlots from '../pages/manipulations/Slots';
import ManipulationsAttendance from '../pages/manipulations/Attendance';

import PlateauxList from '../pages/plateaux/List';
import PlateauxView from '../pages/plateaux/View';
import PlateauxForm from '../pages/plateaux/Form';

import BookingHistoryPage from '../pages/BookingHistory';
import SettingsPage from '../pages/Settings';
import LegalPage from '../pages/Legal';

const SiteMap = [
    {
        title:         'Accueil',
        icon:          (<HomeIcon />),
        url:           '/',
        exactPath:     true,
        showInMenu:    true,
        component:     LandingPage,
        authenticated: false
    },
    {
        title:         'Connexion',
        icon:          false,
        url:           '/login',
        exactPath:     false,
        showInMenu:    false,
        component:     LoginPage,
        authenticated: false
    },
    {
        title:         'Mot de passe oublié',
        icon:          false,
        url:           '/forgotpassword',
        exactPath:     false,
        showInMenu:    false,
        component:     ForgotPasswordPage,
        authenticated: false
    },
    {
        title:         'Réinitialisation du mot de passe',
        icon:          false,
        url:           '/resetpassword',
        exactPath:     false,
        showInMenu:    false,
        component:     ResetPasswordPage,
        authenticated: false
    },
    {
        title:         'Profil',
        icon:          false,
        url:           '/profile',
        exactPath:     false,
        showInMenu:    false,
        component:     ProfilePage,
        authenticated: false
    },
    {
        title:         'Dashboard',
        icon:          (<DashboardIcon />),
        url:           '/dashboard',
        exactPath:     false,
        showInMenu:    true,
        component:     DashboardPage,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP', 'PLAT']
    },
    '---',
    ////////////////
    /// PLATEAUX ///
    ////////////////
    {
        title:         'Plateaux',
        icon:          (<MeetingRoomIcon />),
        url:           '/plateaux',
        exactPath:     true,
        showInMenu:    true,
        component:     PlateauxList,
        authenticated: true,
        roles:         ['ADMIN', 'PLAT']
    },
    {
        title:         'Créer un plateau',
        icon:          false,
        url:           '/plateaux/new',
        exactPath:     true,
        showInMenu:    false,
        component:     PlateauxForm,
        authenticated: true,
        roles:         ['ADMIN', 'PLAT']
    },
    {
        title:         'Modifier un plateau',
        icon:          false,
        url:           '/plateaux/:id/edit',
        exactPath:     true,
        showInMenu:    false,
        component:     PlateauxForm,
        authenticated: true,
        roles:         ['ADMIN', 'PLAT']
    },
    {
        title:         'Visualiser un plateau',
        icon:          false,
        url:           '/plateaux/:id',
        exactPath:     false,
        showInMenu:    false,
        component:     PlateauxView,
        authenticated: true,
        roles:         ['ADMIN', 'PLAT']
    },
    /////////////////////
    /// MANIPULATIONS ///
    /////////////////////
    {
        title:         'Manipulations',
        icon:          (<ShutterSpeedIcon />),
        url:           '/manipulations',
        exactPath:     true,
        showInMenu:    true,
        component:     ManipulationsList,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    {
        title:         'Créer une manipulation',
        icon:          false,
        url:           '/manipulations/new',
        exactPath:     true,
        showInMenu:    false,
        component:     ManipulationsForm,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    {
        title:         'Modifier une manipulation',
        icon:          false,
        url:           '/manipulations/:id/edit',
        exactPath:     true,
        showInMenu:    false,
        component:     ManipulationsForm,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    {
        title:         'Gérer les créneaux',
        icon:          false,
        url:           '/manipulations/:id/slots',
        exactPath:     true,
        showInMenu:    false,
        component:     ManipulationsSlots,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    {
        title:         'Gérer la présence',
        icon:          false,
        url:           '/manipulations/:id/attendance',
        exactPath:     true,
        showInMenu:    false,
        component:     ManipulationsAttendance,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    {
        title:         'Visualiser une manipulation',
        icon:          false,
        url:           '/manipulations/:id',
        exactPath:     false,
        showInMenu:    false,
        component:     ManipulationsView,
        authenticated: true,
        roles:         ['ADMIN', 'MANIP']
    },
    ////////////////
    /// MATERIEL ///
    ////////////////
    {
        title:         'Matériel',
        icon:          (<BuildIcon />),
        url:           '/equipments',
        exactPath:     true,
        showInMenu:    true,
        component:     EquipmentsList,
        authenticated: true,
        roles:         ['ADMIN', 'PLAT', 'MANIP']
    },
    {
        title:         'Créer un matériel',
        icon:          false,
        url:           '/equipments/new',
        exactPath:     true,
        showInMenu:    false,
        component:     EquipmentsForm,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Modifier un matériel',
        icon:          false,
        url:           '/equipments/:id/edit',
        exactPath:     true,
        showInMenu:    false,
        component:     EquipmentsForm,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Visualiser un matériel',
        icon:          false,
        url:           '/equipments/:id',
        exactPath:     false,
        showInMenu:    false,
        component:     EquipmentsView,
        authenticated: true,
        roles:         ['ADMIN']
    },
    '---',
    ////////////////////
    /// UTILISATEURS ///
    ////////////////////
    {
        title:         'Utilisateurs',
        icon:          (<PeopleIcon />),
        url:           '/users',
        exactPath:     true,
        showInMenu:    true,
        component:     UsersList,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Créer un utilisateur',
        icon:          false,
        url:           '/users/new',
        exactPath:     true,
        showInMenu:    false,
        component:     UsersForm,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Modifier un utilisateur',
        icon:          false,
        url:           '/users/:id/edit',
        exactPath:     true,
        showInMenu:    false,
        component:     UsersForm,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Visualiser un utilisateur',
        icon:          false,
        url:           '/users/:id',
        exactPath:     false,
        showInMenu:    false,
        component:     UsersView,
        authenticated: true,
        roles:         ['ADMIN']
    },
    //////////////////
    /// PARAMETRES ///
    //////////////////
    {
        title:         'Historique',
        icon:          (<EqualizerIcon />),
        url:           '/bookinghistory',
        exactPath:     false,
        showInMenu:    true,
        component:     BookingHistoryPage,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Paramètres',
        icon:          (<SettingsIcon />),
        url:           '/settings',
        exactPath:     false,
        showInMenu:    true,
        component:     SettingsPage,
        authenticated: true,
        roles:         ['ADMIN']
    },
    {
        title:         'Mentions Légales',
        icon:          false,
        url:           '/legal',
        exactPath:     true,
        showInMenu:    false,
        component:     LegalPage,
        authenticated: false
    },
    '---',
];

export default SiteMap;