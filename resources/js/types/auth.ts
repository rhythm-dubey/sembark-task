export type Role = {
    id: number;
    name: string;
    label: string;
};

export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at?: string;
    updated_at?: string;
    company_id: number | null;
    role: Role | null;
    is_super_admin: boolean;
    can_create_short_urls: boolean;
    can_view_short_urls: boolean;
    can_invite: boolean;
};

export type Auth = {
    user: User | null;
};

/* @chisel-passkeys */
export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};
/* @end-chisel-passkeys */

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
