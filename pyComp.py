import re

class PyCompiler:
    def __init__(self, code):
        self.code = code
        self.vars = {}

    def run(self):
        lines = self.parse_lines(self.code)
        lines = list(filter(None, lines))

        for line in lines:
            line = line.strip()

            if line:
                tokens = self.handle_line(line)

                if tokens:
                    self.handle_tokens(tokens)

    def parse_lines(self, lines):
        return lines.split("\n")

    def handle_line(self, line):
        replacements = {
            "==": " == ",
            "!=": " != ",
            ">": " > ",
            "<": " < ",
            "<=": " <= ",
            ">=": " >= ",
            "(": " ( ",
            ")": " ) ",
            ":": " : ",
            ";": " ; ",
            "*": " * ",
            "+": " + ",
            "-": " - ",
            "/": " / ",
        }

        for old, new in replacements.items():
            line = line.replace(old, new)

        if "==" not in line and "!=" not in line:
            line = line.replace("=", " = ")

        line = line.strip()

        # Remove parentheses from variable names
        line = re.sub(r'\((.*?)\)', r'\1', line)

        return line.split()


    def handle_tokens(self, tokens):
        if tokens[0] == 'if':
            self.handle_if(tokens)
        if tokens[0] == 'print':
            self.handle_print(tokens)
        if len(tokens[0]) == 1:
            self.handle_assignment(tokens)

    def handle_if(self, tokens):
        line = " ".join(tokens)
        line = line.split(";")
        line[1] = line[1].strip()

        condition = line[0].split(" : ")[0].strip()[3:].strip()  # Remove "if" and trim
        temp = list(filter(None, condition.split()))

        if len(temp) > 1:
            operator = temp[1]
            var1 = int(temp[0]) if temp[0].isdigit() else self.vars[temp[0]]
            var2 = int(temp[2]) if temp[2].isdigit() else self.vars[temp[2]]
            result = self.handle_operator(operator, var1, var2)
        else:
            result = temp[0]

        if result:
            operation = line[0].split(" : ")[1].strip()
            self.handle_tokens(operation.split())
        elif line[1]:
            self.handle_else(line[1].split())


    def handle_operator(self, operator, var1, var2):
        if operator == '==':
            return var1 == var2
        elif operator == '!=':
            return var1 != var2
        elif operator == '>':
            return var1 > var2
        elif operator == '<':
            return var1 < var2
        elif operator == '<=':
            return var1 <= var2
        elif operator == '>=':
            return var1 >= var2

    def handle_else(self, tokens):
        operation = " ".join(tokens).split(" : ")[1].strip()
        self.handle_tokens(operation.split())

    def handle_print(self, tokens):
        print_val = " ".join(tokens).replace("print", "").replace("(", "").replace(")", "").replace("'", "").strip()
        print(print_val)

    def handle_assignment(self, tokens):
        if len(tokens) == 3:
            assignment = tokens[1]
            if assignment == "=":
                if tokens[2].isdigit():
                    self.vars[tokens[0]] = int(tokens[2])
                elif tokens[2] in self.vars:
                    self.vars[tokens[0]] = int(self.vars[tokens[2]])
                else:
                    print("Error! Wrong assignment variables.")
            else:
                print("Error! Wrong assignment operator.")
        elif len(tokens) > 3:
            token = " ".join(tokens)
            token = token.split("=")
            token[1] = token[1].strip()
            token[0] = token[0].strip()
            handlers = token[1].split()
            operator = handlers[1]
            var1 = int(handlers[0]) if handlers[0].isdigit() else self.vars[handlers[0]]
            var2 = int(handlers[2]) if handlers[2].isdigit() else self.vars[handlers[2]]
            result = self.handle_assign_operator(operator, var1, var2)
            self.vars[token[0]] = result
        else:
            print("Error! Wrong assignment format.")

    def handle_assign_operator(self, operator, var1, var2):
        if operator == '*':
            return var1 * var2
        elif operator == '/':
            return var1 / var2
        elif operator == '+':
            return var1 + var2
        elif operator == '-':
            return var1 - var2


code = """
y = 14
x = 12
if x != y : print 'not equal' ; else : print 'equal'
"""

obj = PyCompiler(code)
obj.run()

#code samples
    # code0 = """
    # y = 14
    # x = 12
    # if x != y : print 'not equal' ; else : print 'equal'
    # """
    # code00 = """
    # y = 14
    # x = y/2
    # if x != 7 : print 'not equal' ; else : print 'equal'
    # """
    # code1 = """
    # y=14
    # x=y/2
    # if(x==7):print('equal');else:print('not equal')
    # """
    # code2 = """
    # y=14
    # x=y/2
    # if (x==7):print('equal');else:print('not equal')
    # """
    # code3 = """
    # y =14
    # x =y/2
    # if (x==7):print('equal');else:print('not equal')
    # """
    # code4 = """
    # y= 14
    # x= y/2
    # if (x==7):print('equal');else:print('not equal')
    # """
    # code5 = """
    # y = 14
    # x = y/2
    # if (x==7):print('equal');else:print('not equal')
    # """
    # code6 = """
    # y = 14
    # x = y / 2
    # if (x==7):print('equal');else:print('not equal')
    # """
    # code7 = """
    # y = 14
    # x = y / 2
    # if ( x==7 ):print('equal');else:print('not equal')
    # """
    # code8 = """
    # y = 14
    # x = y / 2
    # if ( x == 7 ):print('equal');else:print('not equal')
    # """
    # code9 = """
    # y = 14
    # x = y / 2
    # if ( x == 7 ) : print ('equal') ; else : print ('not equal')
    # """
    # code10 = """
    # y = 14
    # x = y / 2
    # if ( x == 7 ) : print ( 'equal' ) ; else : print ( 'not equal' )
    # """
    # code11 = """
    # y = 14
    # x = y / 2
    # if x == 7 : print 'equal' ; else : print 'not equal'
    # """
    # code12 = """
    # y = 14
    # x = 14
    # if x == y : print 'equal' ; else : print 'not equal'
    # """