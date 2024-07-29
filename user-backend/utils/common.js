import _ from "lodash";
import hbs from "handlebars";

export const replacePlaceholder = async ({inputString, placeholderData}) => {
    const template = hbs.compile(inputString);
    return template(placeholderData);
}