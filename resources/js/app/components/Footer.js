import React from 'react';
import Typography from '@material-ui/core/Typography';
import Link from '@material-ui/core/Link';
import Box from '@material-ui/core/Box';

import RouterLink from './RouterLink';

export default function Footer() {
    return (
        <Box mt={8}>
            <Typography variant="body2" color="textSecondary" align="center">
                {'Copyright © '}
                <Link component={RouterLink} to="/" color="inherit">
                    Expériences CCU
                </Link>
                {' '}
                {new Date().getFullYear()}
                {'. - '}
                <Link component={RouterLink} to="/legal" color="inherit">
                    Mentions Légales
                </Link>
            </Typography>
        </Box>
    );
}
