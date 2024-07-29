import React from "react";
import Skeleton from "react-loading-skeleton";

export const TreeViewSkeleton = () => {
  return (
    <>
      <div id="treeViewSkeleton">
        <div className="">
          <Skeleton circle width={48} height={48} />
        </div>
        <div className="skeleton">
          <Skeleton width={200} height={22} borderRadius={10} />
          <Skeleton width={200} height={22} borderRadius={10} />
        </div>
      </div>
    </>
  );
};

export const TreeViewSingleLoader = () => {
  return (
    <>
      <div id="treeViewSkeleton">
        <div className="">
          <Skeleton circle width={48} height={48} />
        </div>
        <div className="skeleton">
          <Skeleton width={200} height={22} borderRadius={10} />
          <Skeleton width={200} height={22} borderRadius={10} />
        </div>
      </div>
    </>
  );
};
