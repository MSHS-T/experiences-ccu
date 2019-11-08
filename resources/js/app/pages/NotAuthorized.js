import React, { Component } from 'react';

export default class NotAuthorized extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div>
                Not Authorized.
                Required Roles :
                <ul>
                    {/* Improve by showing real role names */}
                    {this.props.requiredRoles.map((role, index) => (
                        <li key={index}>{role}</li>
                    ))}
                </ul>
            </div>
        )
    }
}
