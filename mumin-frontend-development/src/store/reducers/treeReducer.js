import { createSlice } from "@reduxjs/toolkit";
const initialState = {
  genealogyList: { data: {} },
  treeViewList: { data: {} },
  sponserTreeList: { data: {} },
  backToParent:false,
  sponserBackToParent: false,
};

const treeSlice = createSlice({
  name: "tree",
  initialState,
  reducers: {
    setGenealogyTreeList: (state, action) => {
      state.genealogyList = action.payload;
    },
    updateTreeNode: (state, action) => {
      const { nodeId, children} = action.payload;
      const findAndModifyChildrenById = (node, idToFind, newChildrenData) => {
        if (node?.attributes?.id === idToFind) {
          return { ...node, children: newChildrenData };
        }

        const updatedChildren = node?.children?.map((child) => {
          return findAndModifyChildrenById(child, idToFind, newChildrenData);
        });

        return { ...node, children: updatedChildren };
      };

      const updatedGenealogyList = findAndModifyChildrenById(
        state.genealogyList,
        nodeId,
        children
      );
      state.genealogyList = updatedGenealogyList;
    },
    setTreeViewList: (state, action) => {
      state.treeViewList.data = action.payload;
    },
    updateTreeViewList: (state, action) => {
      const { nodeId, children } = action.payload;
      const findAndModifyChildrenById = (data, idToFind, newChildrenData) => {
        return data?.map((item) => {
          if (item.id === idToFind) {
            return {
              ...item,
              children: newChildrenData,
            };
          } else if (item.children.length > 0) {
            return {
              ...item,
              children: findAndModifyChildrenById(
                item.children,
                idToFind,
                newChildrenData
              ),
            };
          }
          return item;
        });
      };

      const updatedTreeViewListData = findAndModifyChildrenById(
        state.treeViewList.data,
        nodeId,
        children
      );

      state.treeViewList.data = updatedTreeViewListData;
    },
    setSponserTreeList: (state, action) => {
      state.sponserTreeList = action.payload;
    },
    updateSponserTreeList: (state, action) => {
      const { nodeId, children } = action.payload;
      const findAndModifyChildrenById = (node, idToFind, newChildrenData) => {
        if (node?.attributes?.id === idToFind) {
          return { ...node, children: newChildrenData };
        }

        const updatedChildren = node?.children?.map((child) => {
          return findAndModifyChildrenById(child, idToFind, newChildrenData);
        });

        return { ...node, children: updatedChildren };
      };

      const updateSponserTreeList = findAndModifyChildrenById(
        state.sponserTreeList,
        nodeId,
        children
      );
      state.sponserTreeList = updateSponserTreeList;
    },

    updateUnilevelGenealogyTree: (state, action) => {
      const { fatherId, position, newChildren } = action.payload;
      const addChildToTree = (treeData, currentPosition) => {
        if (treeData?.attributes?.id === fatherId) {
          // Clone the treeData and add each new child to the cloned array
          const newChildrenArray = [...treeData.children];
          newChildrenArray.splice(currentPosition - 1, 1, ...newChildren); // Replace existing node
    
          // Return the cloned treeData with the updated children array
          return {
            ...treeData,
            children: newChildrenArray,
          };
        } else {
          // Return the treeData with the children array updated using map
          return {
            ...treeData,
            children: treeData?.children?.map((node) => addChildToTree(node, currentPosition)),
          };
        }
      };
    
      // Clone the state.genealogyList and apply the addChildToTree function
      const updatedUnilevelTree = addChildToTree({ ...state.genealogyList }, position);
    
      return {
        ...state,
        genealogyList: updatedUnilevelTree,
      };
    },
    updateSponserTree: (state, action) => {
      const { fatherId, position, newChildren } = action.payload;

      const addChildToTree = (treeData, currentPosition) => {
        if (treeData?.attributes?.id === fatherId) {
          // Clone the treeData and add each new child to the cloned array
          const newChildrenArray = [...treeData.children];
          newChildrenArray.splice(currentPosition - 1, 1, ...newChildren); // Replace existing node
    
          // Return the cloned treeData with the updated children array
          return {
            ...treeData,
            children: newChildrenArray,
          };
        } else {
          // Return the treeData with the children array updated using map
          return {
            ...treeData,
            children: treeData?.children?.map((node) => addChildToTree(node, currentPosition)),
          };
        }
      };
      // Clone the state.genealogyList and apply the addChildToTree function
      const updatedSponserTree = addChildToTree({ ...state.sponserTreeList }, position);
    
      return {
        ...state,
        sponserTreeList: updatedSponserTree,
      };
    },
    enableBackToParent:(state) => {
      state.backToParent = true;
    },
    disableBackToParent:(state) => {
      state.backToParent = false;
    },
    enableSponserBackToParent: (state) => {
      state.sponserBackToParent = true;
    },
    disableSponserBackToParent: (state) => {
      state.sponserBackToParent = false;
    }
  },
});

export const {
  setGenealogyTreeList,
  updateTreeNode,
  setTreeViewList,
  updateTreeViewList,
  setSponserTreeList,
  updateSponserTreeList,
  updateUnilevelGenealogyTree,
  enableBackToParent,
  disableBackToParent,
  enableSponserBackToParent,
  disableSponserBackToParent,
  updateSponserTree
} = treeSlice.actions;

export default treeSlice.reducer;
