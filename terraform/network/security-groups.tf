# security-groups.tf

# Security Group del ALB: acepta trafico HTTP/HTTPS desde Internet
resource "aws_security_group" "alb" {
  name        = "${var.project_name}-alb-sg"
  description = "Permite HTTP y HTTPS desde Internet hacia el ALB"
  vpc_id      = aws_vpc.main.id

  ingress {
    description = "HTTP desde Internet"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS desde Internet"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "Todo el trafico saliente permitido"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name    = "${var.project_name}-alb-sg"
    Project = var.project_name
  }
}

# Security Group de la capa de aplicacion: solo acepta trafico del ALB
resource "aws_security_group" "app" {
  name        = "${var.project_name}-app-sg"
  description = "Permite trafico HTTP solo desde el ALB"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "HTTP desde el ALB"
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb.id]
  }

  egress {
    description = "Todo el trafico saliente permitido"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name    = "${var.project_name}-app-sg"
    Project = var.project_name
  }
}

# Security Group de la base de datos: solo acepta MySQL desde la capa de app
resource "aws_security_group" "db" {
  name        = "${var.project_name}-db-sg"
  description = "Permite trafico MySQL solo desde la capa de aplicacion"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "MySQL desde la capa de app"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.app.id]
  }

  tags = {
    Name    = "${var.project_name}-db-sg"
    Project = var.project_name
  }
}