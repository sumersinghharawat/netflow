const getDynamicTime = async () => {
    try {
        let url = "/admin/session-time";
        const res = await $.get(`${url}`);
        var time = res.time;
        var status = res.active;
        if(status == 1){
            const res2 = await $.sessionTimeout({
                keepAliveUrl: "/",
                logoutButton: "Logout",
                logoutUrl: "/auto/logout",
                redirUrl: "/lockscreen",
                // warnAfter: time * 1e3,
                // redirAfter: (time + 10) * 1e3,
                warnAfter: time,
                redirAfter: time + (1e4),
                countdownMessage: "Redirecting in {timer} seconds.",
            });
            $("#session-timeout-dialog  [data-dismiss=modal]").attr(
                "data-bs-dismiss",
                "modal"
            );
        }
        return true;

    } catch (error) {
        console.log(error);
    }
};

getDynamicTime();