import React from 'react';
import { Typography, Container, Box, Grid, Button } from '@material-ui/core';
import Footer from '../components/Footer';

export default function Settings(props) {
    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Mentions Légales'}
            </Typography>
            <hr />
            <Container maxWidth="md" component="main">
                <p>Non ullamco amet nisi occaecat ullamco reprehenderit cupidatat tempor. Et exercitation ea ad esse aliqua. Consectetur sint elit laboris eiusmod mollit eu velit tempor veniam tempor aliqua consequat esse in. Ut consectetur esse fugiat exercitation non dolor dolor laborum sunt.</p>
                <p>Dolor cupidatat officia velit proident dolor culpa. Magna pariatur pariatur velit exercitation. Do adipisicing dolore dolor exercitation occaecat. Laboris culpa ut officia ex anim sint esse Lorem consectetur et do do. Ullamco nostrud veniam cillum sint nulla adipisicing in. Non pariatur proident amet nisi qui proident Lorem minim incididunt qui ut dolore. Tempor non ullamco ad aliqua pariatur esse laborum enim ullamco.</p>
                <p>Sit duis consequat laborum sunt pariatur in tempor reprehenderit nisi sint irure sit. Id velit id fugiat adipisicing eu adipisicing enim consectetur eu proident. Ea amet cupidatat magna anim. Fugiat occaecat commodo laboris occaecat ullamco laborum. Deserunt enim labore mollit sint occaecat do ipsum ex. Voluptate in duis labore ipsum duis.</p>
                <p>Non ut labore pariatur duis laborum et qui amet. Anim duis sunt sint dolore velit amet reprehenderit Lorem magna proident mollit. Consectetur laboris enim consectetur dolore eiusmod in cupidatat non.</p>
                <p>Aliqua est anim aliqua officia elit ut eiusmod non est. Fugiat consectetur amet irure nulla proident cillum ex consequat ut laborum dolore. Voluptate officia culpa consequat qui irure pariatur velit mollit qui proident minim. Nisi laboris amet anim cillum reprehenderit. Et velit est pariatur nulla ad. Cupidatat commodo duis eu in quis occaecat Lorem occaecat officia minim. Aute nisi amet adipisicing sunt occaecat laborum ullamco cillum.</p>
                <p>Excepteur aute minim fugiat et ea eiusmod ex reprehenderit aliqua pariatur minim reprehenderit ad in. Voluptate eiusmod do ea magna veniam ex anim eiusmod. Culpa dolor in exercitation non qui. Occaecat adipisicing Lorem ex veniam irure anim in adipisicing cillum. Do quis voluptate ad aliquip incididunt culpa irure est cillum exercitation laboris occaecat anim. Aliquip nostrud deserunt voluptate non fugiat non. Minim exercitation ut ipsum est excepteur eiusmod sint proident reprehenderit nisi cillum qui voluptate.</p>

                <Box style={{ marginTop: '40px' }}>
                    <Grid container spacing={5} alignItems="center" justify="center">
                        <Button
                            variant="contained"
                            color="default"
                            onClick={props.history.goBack}
                        >
                        Retour
                        </Button>
                    </Grid>
                </Box>
            </Container>
            <Footer />
        </>
    );
}
