export const init = (strs) => {
    // profile_field_Muttersprache
    const pfmspr = document.querySelector(strs[0]);
    if(pfmspr) {
        pfmspr.placeholder = strs[1];
    }
};
