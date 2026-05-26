const validateMiddleware = (req, res, next) => {
    // 1. Inspect the incoming data using Joi
    const { error } = authValidator.validate(req.body);
    
    if (error) {
        // 2. STOP! Bad data. Kick them back to the login page immediately.
        // We DO NOT call next() here.
        return res.render('auth/login', { error: error.details[0].message });
    }
    
    // 3. Data is clean! Let them proceed to the database check.
    next();
};