// Interceptor validating data formatting matches prior to executing database workflows
const validateMiddleware = (req, res, next) => {
    const { error } = authValidator.validate(req.body);
    if (error) {
        // 👇 FIXED: Points directly to 'login'
        return res.render('login', { error: error.details[0].message });
    }
    next();
};