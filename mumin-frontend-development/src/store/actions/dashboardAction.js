import DashboardService from "../../services/dashboard/Dashboard"


export const AppLayout = async () => {
    try {
        const response = await DashboardService.appLayout();
        return response
    } catch (error) {
        return error.message
    }
}

export const DashboardUserProfile = async () => {
    try {
        const response = await DashboardService.dashboardProfile();
        return response
    } catch (error) {
        return error.message
    }
}

export const DashboardTiles = async () => {
    try {
        const response = await DashboardService.dashboardTiles();
        return response
    } catch (error) {
        return error.message
    }
}

export const GetGraph = async (params) => {
    try {
        const response = await DashboardService.getGraph(params);
        return response
    } catch (error) {
        return error.message
    }
}

export const NotificationData = async () => {
    try {
        const response = await DashboardService.notificationCall();
        return response
    } catch (error) {
        console.log(error.message);
    }
}

export const ReadAllNotification = async () => {
    try {
        const response = await DashboardService.ReadAllNotification();
        return response
    } catch (error) {
        console.log(error.message)
    }
}

export const DashboardDetails = async () => {
    try {
        const response = await DashboardService.dashboardDetails();
        return response
    } catch (error) {
        return error.message
    }
}

export const PackageOverview = async () => {
    try {
        const response = await DashboardService.packageOverview();
        return response
    } catch (error) {
        return error.message
    }
}

export const RankOverview = async () => {
    try {
        const response = await DashboardService.rankOverview();
        return response
    } catch (error) {
        return error.message
    }
}

export const TopRecruiters = async () => {
    try {
        const response = await DashboardService.topRecruiters();
        return response
    } catch (error) {
        return error.message
    }
}

export const DashboardExpenses = async () => {
    try {
        const response = await DashboardService.dashboardExpenses();
        return response
    } catch (error) {
        return error.message
    }
}

