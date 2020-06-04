import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { Table, TableHead, TableRow, TableCell, TableBody } from '@material-ui/core';

class DayTimeTable extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        const {
            calcCellHeight,
            caption,
            cellKey,
            data,
            hideHeaders,
            hideTimes,
            isActive,
            rowNum,
            showCell,
            showHeader,
            showTime,
            tableProps,
            timeText,
            toolTip,
            valueKey
        } = this.props;

        var headers = data.map((day, ii) => (
            <TableCell key={ii}>{showHeader(day)}</TableCell>
        ));
        var colNum = headers.length;
        var grid = [];
        var found = new Map();

        for (let ii = 0; ii < rowNum; ii++) {
            grid[ii] = [];
            for (let jj = 0; jj < colNum; jj++) {
                grid[ii][jj] = 0;
                data[jj][valueKey].map(cell => {
                    if (isActive(cell, ii)) {
                        grid[ii][jj] = {
                            height: calcCellHeight(cell),
                            info:   { ...cell }
                        };
                        if (found.get(cellKey(cell))) {
                            grid[ii][jj].skip = true;
                        } else {
                            grid[ii][jj].first = true;
                            found.set(cellKey(cell), true);
                        }
                    }
                });
            }
        }

        return (
            <Table
                {...tableProps}
            >
                {!hideHeaders && (
                    <TableHead>
                        {!!caption && (
                            <TableRow>
                                <TableCell
                                    colSpan={colNum + !hideTimes}
                                    tooltip={toolTip}
                                    style={{ textAlign: 'center' }}
                                >
                                    {caption}
                                </TableCell>
                            </TableRow>
                        )}
                        <TableRow>
                            {!hideTimes && <TableCell>{timeText}</TableCell>}
                            {headers}
                        </TableRow>
                    </TableHead>
                )}
                <TableBody>
                    {grid.map((row, ii) => {
                        var cellStyle = {
                            borderRight: '1px solid rgb(224, 224, 224)',
                            borderLeft:  '1px solid rgb(224, 224, 224)'
                        };

                        return (
                            <TableRow key={ii}>
                                {!hideTimes && (
                                    <TableCell style={cellStyle}>
                                        {showTime(ii)}
                                    </TableCell>
                                )}
                                {row.map((xx, jj) => {
                                    if (!xx.info) {
                                        return (
                                            <TableCell key={`${ii}-${jj}`} style={cellStyle} />
                                        );
                                    } else if (xx.first) {
                                        if (!xx.info.props) {
                                            xx.info.props = { style: '' };
                                        }
                                        return (
                                            <TableCell
                                                key={cellKey(xx.info)}
                                                rowSpan={xx.height}
                                                {...xx.info.props}
                                                style={Object.assign(xx.info.props.style, cellStyle)}
                                            >
                                                {showCell(xx.info)}
                                            </TableCell>
                                        );
                                    } else if (xx.skip) {
                                        return;
                                    }
                                })}
                            </TableRow>
                        );
                    })}
                </TableBody>
            </Table>
        );
    }
}

DayTimeTable.propTypes = {
    calcCellHeight: PropTypes.func.isRequired,
    caption:        PropTypes.object,
    cellKey:        PropTypes.func.isRequired,
    data:           PropTypes.array.isRequired,
    hideHeaders:    PropTypes.bool,
    hideTimes:      PropTypes.bool,
    isActive:       PropTypes.func.isRequired,
    rowNum:         PropTypes.number.isRequired,
    showCell:       PropTypes.func.isRequired,
    showHeader:     PropTypes.func.isRequired,
    showTime:       PropTypes.func.isRequired,
    tableProps:     PropTypes.object,
    timeText:       PropTypes.string,
    toolTip:        PropTypes.string,
    valueKey:       PropTypes.string.isRequired
};

DayTimeTable.defaultProps = {
    timeText: 'Times',
    toolTip:  ''
};

export default DayTimeTable;
