import errCode from './errorCode.js';

export const successMessage = async(req) => { 
    return {
        data : { status: true, data: req.data},
        code: 200 
    }
}

export const errorMessage = async(req) => {
    const error =  {
        code : req.code,
        description : errCode[`${req.code}`],
    }
    return {
        data : {
            status: false, data: error, 
        },
        code: req.statusCode 
    }
}

