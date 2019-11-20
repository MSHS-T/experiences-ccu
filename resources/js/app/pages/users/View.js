import React from 'react'

export default function UserView(props) {
    return (
        <div>
            View User {props.match.params.id}
        </div>
    )
}
