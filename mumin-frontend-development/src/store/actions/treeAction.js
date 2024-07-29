import { GenealogyService,TreeViewService,SponserTreeService,DownlineMembersService,ReferralMembersService } from "../../services/tree/network";
export const GenealogyActions = {
  getTreelist: async (userId, userName) => {
    try{
      const response = await GenealogyService.getTreelist(userId, userName)
      return response
    } catch (error) {
      return error.message
    }
  },
  getUnilevelMore: async (fatherId, position) => {
    try {
      const response = await GenealogyService.getUnilevelMore(fatherId, position)
      return response
    } catch (error) {
      return error.message
    }
  }
};

export const TreeViewActions = {
  getTreelist: async (userId) => {
    try {
      const response = await TreeViewService.getTreelist(userId)
      return response
    } catch (error) {
      return error.message
    }
  }
};

export const SponserTreeActions = {
  getTreelist: async (userId, userName) => {
    try {
      const response = await SponserTreeService.getTreelist(userId, userName)
      return response
    } catch (error) {
      return error.message
    }
  }
};
export const downlineMembersActions={
  getDownlineMembers:async(level,page,itemsPerPage)=>{
    try {
      const response=await DownlineMembersService.callDownline(level,page,itemsPerPage)
      return response
    } catch (error) {
      return error.message
    }
  },
  getDownlineheaders:async()=>{
    try {
      const response=await DownlineMembersService.callHeader()
      return response
    } catch (error) {
      return error.message
    }
  }

}

export const ReferralMembersActions={
  getReferralmembers:async(level,page,itemsPerPage)=>{
    try {
      const response=await ReferralMembersService.callReferral(level,page,itemsPerPage)
      return response
    } catch (error) {
      return error.message
    }
  },
  getRferralHeader:async()=>{
    try {
      const response=await ReferralMembersService.callHeader()
      return response
    } catch (error) {
      return error.message
    }
  }
}