import React from 'react'

export default function View() {
    return (
        <div>
            View User {this.props.match.params.id}
        </div>
    )
}
