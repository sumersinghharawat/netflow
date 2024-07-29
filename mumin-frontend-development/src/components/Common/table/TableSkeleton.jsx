import React from "react";
import Skeleton from "react-loading-skeleton";

export const TableSkeleton = ({ rowCount, cellCount }) => {
  const rows = [];
  for (let i = 0; i < rowCount; i++) {
    const cells = [];
    for (let j = 0; j < cellCount; j++) {
      cells.push(
        <td key={j}>
          <Skeleton count={2} />
        </td>
      );
    }
    rows.push(<tr key={i}>{cells}</tr>);
  }
  return <>{rows}</>;
};
