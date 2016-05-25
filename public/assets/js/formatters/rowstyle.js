function rowstyleFormatter(row, index) {
    if (row.rowstyle){
        return {
            classes: row.rowstyle
        };
    }
    return {};
}