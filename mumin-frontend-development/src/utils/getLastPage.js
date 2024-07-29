
export const getLastPage = (pageSize, totalCount) => {
    const result = Math.ceil(totalCount / pageSize)
    return result
}