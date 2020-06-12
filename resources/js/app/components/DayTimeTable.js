import React, { Component } from 'react';
import PropTypes from 'prop-types';

import { Table, TableHead, TableRow, TableCell, TableBody, TableFooter } from '@material-ui/core';

class DayTimeTable extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        const {
            calcCellHeight,
            caption,
            cellKey,
            cellStyle,
            data,
            hideHeaders,
            hideTimes,
            isActive,
            rowNum,
            rowStyle,
            showCell,
            showHeader,
            showFooter,
            showTime,
            tableProps,
            timeText,
            toolTip,
            valueKey
        } = this.props;

        var headers = data.map((day, ii) => (
            <TableCell key={ii} style={{ borderBottom: '1px solid rgb(180, 180, 180)' }}>{showHeader(day)}</TableCell>
        ));
        var footers = data.map((day, ii) => (
            <TableCell key={ii}>{showFooter(day)}</TableCell>
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

        const defaultCellStyle = {
            height: 'auto !important',
            border: '1px solid rgb(180, 180, 180)',
            // borderLeft:  '1px solid rgb(224, 224, 224)'
        };

        const defaultTimeCellStyle = {
            height:      'auto !important',
            border:      'none',
            borderRight: '1px solid rgb(180, 180, 180)',
        };

        return (
            <Table
                {...tableProps}
            >
                <colgroup>
                    {!hideTimes && <col width="5%" />}
                    {headers.map((h, i) => <col key={'colWidth_'+i} width={(!hideTimes ? 95 : 100)/(headers.length) + '%'}/>)}
                </colgroup>
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
                        return (
                            <TableRow key={ii} style={rowStyle}>
                                {!hideTimes && (
                                    <TableCell style={{ ...cellStyle(ii, -1, null, row), ...defaultTimeCellStyle }}>
                                        {showTime(ii)}
                                    </TableCell>
                                )}
                                {row.map((xx, jj) => {
                                    const computedCellStyle = { ...cellStyle(ii, jj, xx, row), ...defaultCellStyle };
                                    if (!xx.info) {
                                        return (
                                            <TableCell key={`${ii}-${jj}`} style={computedCellStyle} />
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
                                                style={Object.assign(xx.info.props.style, computedCellStyle)}
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
                <TableFooter>
                    <TableRow>
                        {!hideTimes && <TableCell>{timeText}</TableCell>}
                        {footers}
                    </TableRow>
                </TableFooter>
            </Table>
        );
    }
}

DayTimeTable.propTypes = {
    calcCellHeight: PropTypes.func.isRequired,
    caption:        PropTypes.object,
    cellKey:        PropTypes.func.isRequired,
    cellStyle:      PropTypes.func,
    data:           PropTypes.array.isRequired,
    hideHeaders:    PropTypes.bool,
    hideTimes:      PropTypes.bool,
    isActive:       PropTypes.func.isRequired,
    rowNum:         PropTypes.number.isRequired,
    rowStyle:       PropTypes.object,
    showCell:       PropTypes.func.isRequired,
    showHeader:     PropTypes.func.isRequired,
    showTime:       PropTypes.func.isRequired,
    tableProps:     PropTypes.object,
    timeText:       PropTypes.string,
    toolTip:        PropTypes.string,
    valueKey:       PropTypes.string.isRequired
};

DayTimeTable.defaultProps = {
    cellStyle: () => {},
    rowStyle:  {},
    timeText:  'Times',
    toolTip:   ''
};

export default DayTimeTable;
