const winston = require('winston');
const path = require('path');

// Dynamically target absolute log tracking directories
const LOG_DIRECTORY = path.join(__dirname, '../logs');

const logger = winston.createLogger({
    // Global baseline collection priority layer
    level: 'info',
    
    // Structure pipeline definitions for data management
    format: winston.format.combine(
        winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }),
        winston.format.errors({ stack: true }),
        winston.format.json()
    ),
    
    transports: [
        new winston.transports.File({ 
            filename: path.join(LOG_DIRECTORY, 'error.log'), 
            level: 'error',
            maxsize: 5242880, 
            maxFiles: 5         
        }),
        
        new winston.transports.File({ 
            filename: path.join(LOG_DIRECTORY, 'combined.log'),
            maxsize: 10485760, 
            maxFiles: 7        
        })
    ]
});

if (process.env.NODE_ENV !== 'production') {
    logger.add(new winston.transports.Console({
        format: winston.format.combine(
            winston.format.colorize(),
            winston.format.simple()
        )
    }));
}

module.exports = logger;