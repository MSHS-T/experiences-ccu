import React from 'react';
import Badge from '@material-ui/core/Badge';
import Box from '@material-ui/core/Box';
import Button from '@material-ui/core/Button';
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import CardHeader from '@material-ui/core/CardHeader';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import Container from '@material-ui/core/Container';
import { makeStyles } from '@material-ui/core/styles';

import Footer from '../components/Footer';
import { Avatar } from '@material-ui/core';

const isDarkMode = window.localStorage.getItem('theme') === 'dark';

const useStyles = makeStyles(theme => ({
    '@global': {
        ul: {
            margin:  0,
            padding: 0,
        },
        li: {
            listStyle: 'none',
        },
    },
    avatar: {
        margin:          theme.spacing(1),
        backgroundColor: theme.palette.grey[isDarkMode ? 800 : 200],
        width:           60,
        height:          60
    },
    button: {
        margin: theme.spacing(1, 1.5),
    },
    heroContent: {
        padding:       theme.spacing(6, 0, 6),
        display:       'flex',
        flexDirection: 'column',
        alignItems:    'center',
    },
    cardHeader: {
        backgroundColor: theme.palette.action.disabledBackground,
    },
    cardExperience: {
        display:        'flex',
        justifyContent: 'center',
        alignItems:     'baseline',
        marginBottom:   theme.spacing(2),
    }
}));

const experiences = [
    {
        title:       'Cillum veniam id in',
        enddate:     '12/03/2020',
        slots:       95,
        description: [
            '10 users included',
            '2 GB of storage',
            'Help center access',
            'Email support'
        ],
    },
    {
        title:       'Pariatur eu velit',
        enddate:     '19/07/2020',
        slots:       11,
        description: [
            '20 users included',
            '10 GB of storage',
            'Help center access',
            'Priority email support',
        ],
    },
    {
        title:       'Quis culpa sint ad',
        enddate:     '20/06/2020',
        slots:       59,
        description: [
            '50 users included',
            '30 GB of storage',
            'Help center access',
            'Phone & email support',
        ],
    },
];

export default function Landing() {
    const classes = useStyles();

    // eslint-disable-next-line no-undef
    const presentation_text = { __html: APP_SETTINGS.presentation_text };

    return (
        <div className={classes.root}>
            {/* Hero unit */}
            <Container maxWidth="sm" component="main" className={classes.heroContent}>
                <Avatar className={classes.avatar}>
                    <img src={`/favicon${isDarkMode ? '-dark-mode' : ''}.png`} alt="Logo" height="45" />
                </Avatar>
                <Typography component="h1" variant="h2" align="center" color="textPrimary" gutterBottom>
                    Expériences CCU
                </Typography>
                <Typography variant="h5" align="center" color="textSecondary" component="div">
                    <div dangerouslySetInnerHTML={presentation_text}></div>
                </Typography>
            </Container>
            {/* End hero unit */}
            <Container maxWidth="md" component="main">
                <Grid container spacing={5} alignItems="flex-end">
                    {experiences.map(exp => {
                        const badge = (<Badge>{exp.slots}</Badge>);
                        return (
                            <Grid item key={exp.title} xs={12} sm={6} md={4}>
                                <Card>
                                    <CardHeader
                                        title={exp.title}
                                        titleTypographyProps={{ align: 'center', component: 'h3', variant: 'h5' }}
                                        action={badge}
                                        className={classes.cardHeader}
                                    />
                                    <CardContent>
                                        <div className={classes.cardExperience}>
                                            <Typography component="h4" variant="h6" color="textPrimary">
                                                {exp.enddate}
                                            </Typography>
                                        </div>
                                        <ul>
                                            {exp.description.map(line => (
                                                <Typography component="li" variant="subtitle1" align="center" key={line}>
                                                    {line}
                                                </Typography>
                                            ))}
                                        </ul>
                                    </CardContent>
                                    <CardActions>
                                        <Button fullWidth variant="contained" color="primary">
                                            Rejoindre
                                        </Button>
                                    </CardActions>
                                </Card>
                            </Grid>
                        );
                    })}
                </Grid>
                <Box my={2}>
                    <Grid container spacing={5} alignItems="center" justify="center">
                        <Button variant="outlined" color="secondary">
                            Voir toutes les expériences
                        </Button>
                    </Grid>
                </Box>
            </Container>

            <Footer />
        </div>
    );
}