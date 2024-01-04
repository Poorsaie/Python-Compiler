#include <iostream>
#include <sstream>
#include <vector>
#include <algorithm>
#include <map>

class PyCompiler
{
private:
    std::string code;
    std::map<std::string, int> vars;

public:
    PyCompiler(std::string code) : code(code) {}

    void run()
    {
        std::istringstream codeStream(code);
        std::string line;

        while (std::getline(codeStream, line))
        {
            line = trim(line);

            if (!line.empty())
            {
                std::vector<std::string> tokens = handleLine(line);

                if (!tokens.empty())
                {
                    handleTokens(tokens);
                }
            }
        }
    }

private:
    std::vector<std::string> parseLines(const std::string &lines)
    {
        std::vector<std::string> result;
        std::istringstream lineStream(lines);
        std::string line;

        while (std::getline(lineStream, line))
        {
            result.push_back(line);
        }

        return result;
    }

    std::vector<std::string> handleLine(const std::string &line)
    {
        std::string modifiedLine = line;

        // Replace operators and symbols with spaces
        replaceAll(modifiedLine, "==", " == ");
        replaceAll(modifiedLine, "!=", " != ");
        replaceAll(modifiedLine, "<=", " <= ");
        replaceAll(modifiedLine, ">=", " >= ");
        replaceAll(modifiedLine, "<", " < ");
        replaceAll(modifiedLine, ">", " > ");
        replaceAll(modifiedLine, "(", " ( ");
        replaceAll(modifiedLine, ")", " ) ");
        replaceAll(modifiedLine, ":", " : ");
        replaceAll(modifiedLine, ";", " ; ");
        replaceAll(modifiedLine, "*", " * ");
        replaceAll(modifiedLine, "+", " + ");
        replaceAll(modifiedLine, "-", " - ");
        replaceAll(modifiedLine, "/", " / ");

        if (modifiedLine.find("=") != std::string::npos)
        {
            replaceAll(modifiedLine, "=", " = ");
        }

        modifiedLine = trim(modifiedLine);

        return split(modifiedLine, ' ');
    }

    void handleTokens(const std::vector<std::string> &tokens)
    {
        if (tokens[0] == "if")
        {
            handleIf(tokens);
        }
        else if (tokens[0] == "print")
        {
            handlePrint(tokens);
        }
        else if (tokens.size() >= 3)
        {
            handleAssignment(tokens);
        }
    }

    void handleIf(const std::vector<std::string> &tokens)
    {

        // Separate if & else
        std::string line = join(tokens, " ");
        std::vector<std::string> parts = split(line, ';');
        parts[1] = trim(parts[1]);

        // If condition
        std::string condition = trim(split(parts[0], ':')[0]);
        condition = replaceAll(condition, "if (", "");
        condition = replaceAll(condition, "if", "");
        condition = replaceAll(condition, "(", "");
        condition = replaceAll(condition, ")", "");
        condition = trim(condition);

        std::vector<std::string> temp = split(condition, ' ');

        if (temp.size() > 1)
        {
            std::string op = temp[1];


            int var1 = getVariableValue(temp[0]);
            int var2 = getVariableValue(temp[2]);
            
            std::cout << "IF : " << condition << std::endl;
        
        bool result = handleOperator(op, var1, var2);
        
            if (result)
            {
                // Operation
                std::string operation = trim(split(parts[0], ':')[1]);
                handleTokens(split(operation, ' '));
            }
            else
            {
                // Else
                if (!parts[1].empty())
                {
                    handleElse(split(parts[1], ' '));
                }
            }
        }
        else
        {
            bool result = stoi(temp[0]);
            if (result)
            {
                // Operation
                std::string operation = trim(split(parts[0], ':')[1]);
                handleTokens(split(operation, ' '));
            }
            else
            {
                // Else
                if (!parts[1].empty())
                {
                    handleElse(split(parts[1], ' '));
                }
            }
        }
    }

    bool handleOperator(const std::string &op, int var1, int var2)
    {
        if (op == "==")
            return var1 == var2;
        else if (op == "!=")
            return var1 != var2;
        else if (op == ">")
            return var1 > var2;
        else if (op == "<")
            return var1 < var2;
        else if (op == "<=")
            return var1 <= var2;
        else if (op == ">=")
            return var1 >= var2;

        return false; // Handle other operators as needed
    }

    void handleElse(const std::vector<std::string> &tokens)
    {
        std::string line = join(tokens, " ");
        std::string operation = trim(split(line, ':')[1]);
        handleTokens(split(operation, ' '));
    }

    void handlePrint(const std::vector<std::string> &tokens)
    {
        std::string printValue = trim(join(tokens, " "));
        printValue = replaceAll(printValue, "print (", "");
        printValue = replaceAll(printValue, "print", "");
        printValue = replaceAll(printValue, "(", "");
        printValue = replaceAll(printValue, ")", "");
        printValue = replaceAll(printValue, "'", "");
        std::cout << printValue;
    }

    void handleAssignment(const std::vector<std::string> &tokens)
    {

        if (tokens.size() == 3)
        {
            if (tokens[1] == "=")
            {
                // Check if the right-hand side is an integer
                try
                {
                    int value = std::stoi(tokens[2]);
                    vars[tokens[0]] = value;
                }
                catch (const std::invalid_argument &ia)
                {
                    // If not an integer, check if it's an existing variable
                    if (vars.find(tokens[2]) != vars.end())
                    {
                        vars[tokens[0]] = vars[tokens[2]];
                    }
                    else
                    {
                        std::cerr << "Error! Wrong assignment variables." << std::endl;
                    }
                }
    
            }
            else
            {
                std::cerr << "Error! Wrong assignment operator." << std::endl;
            }
            std::cout << tokens[0] << " = " << tokens[2] << std::endl;
        }
        else if (tokens.size() > 3)
        {
            std::string token = join(tokens, " ");
            std::vector<std::string> tokenParts = split(token, '=');
            tokenParts[1] = trim(tokenParts[1]);
            tokenParts[0] = trim(tokenParts[0]);
    
            std::vector<std::string> handlers = split(tokenParts[1], ' ');
    
            // Math
            std::string op = handlers[1];
    
            int var1 = getVariableValue(tokens[2]);
            int var2 = std::stoi(tokens[4]);
            int res = handleAssignOperator(tokens[3] , var1 , var2);
    
            // Store the result in the vars map
            try
            {
                vars[tokenParts[0]] = res;
            }
            catch (const std::invalid_argument &ia)
            {
                // If not an integer, check if it's an existing variable
                if (vars.find(tokenParts[1]) != vars.end())
                {
                    vars[tokenParts[0]] = vars[tokenParts[1]];
                }
                else
                {
                    std::cerr << "Error! Wrong assignment variables." << std::endl;
                }
            }
            std::cout << tokens[0] << " = " << res << std::endl;
    
        }
        else
        {
            std::cerr << "Error! Wrong assignment format." << std::endl;
        }
    }


    int handleAssignOperator(const std::string &op, int var1, int var2)
    {

        if (op == "*")
            return var1 * var2;
        else if (op == "/")
            return var1 / var2;
        else if (op == "+")
            return var1 + var2;
        else if (op == "-")
            return var1 - var2;
    
        return 0; // Handle other operators as needed
    }


    int getVariableValue(const std::string &variable)
    {
        if (isInteger(variable))
        {
            return stoi(variable);
        }
        else if (vars.find(variable) != vars.end())
        {
            return vars[variable];
        }
        else
        {
            std::cerr << "Error! Unknown variable: " << variable << std::endl;
            return 0;
        }
    }


    bool isInteger(const std::string &str)
    {
        return !str.empty() && std::all_of(str.begin(), str.end(), ::isdigit);
    }

    std::string trim(const std::string &s)
    {
        size_t start = s.find_first_not_of(" \t\n\r\f\v");
        size_t end = s.find_last_not_of(" \t\n\r\f\v");
        return (start != std::string::npos && end != std::string::npos) ? s.substr(start, end - start + 1) : "";
    }

    std::vector<std::string> split(const std::string &s, char delim)
    {
        std::vector<std::string> tokens;
        std::istringstream tokenStream(s);
        std::string token;

        while (std::getline(tokenStream, token, delim))
        {
            tokens.push_back(token);
        }

        return tokens;
    }

    std::string join(const std::vector<std::string> &v, const std::string &delimiter)
    {
        std::string result;
        for (size_t i = 0; i < v.size(); ++i)
        {
            result += v[i];
            if (i < v.size() - 1)
            {
                result += delimiter;
            }
        }
        return result;
    }

    std::string replaceAll(std::string str, const std::string &search, const std::string &replace)
    {
        size_t pos = 0;
        while ((pos = str.find(search, pos)) != std::string::npos)
        {
            str.replace(pos, search.length(), replace);
            pos += replace.length();
        }
        return str;
    }
};

int main()
{
    std::string code = "y = 14\nx = 12 \nif x != y : print 'not equal' ; else : print 'equal'";
    PyCompiler obj(code);
    obj.run();

    return 0;
}
