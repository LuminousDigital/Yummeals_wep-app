import LeaderboardComponent from "../../components/admin/leaderboard/LeaderboardComponent";

export default [
    {
        path: "/admin/referral-leaderboard",
        component: LeaderboardComponent,
        name: "admin.referral.leaderboard",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "referral-leaderboard",
            breadcrumb: "leaderboard",
        },
    },
];
