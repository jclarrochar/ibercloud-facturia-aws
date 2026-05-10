# vpc.tf

# La VPC: nuestra red privada virtual
resource "aws_vpc" "main" {
  cidr_block           = var.vpc_cidr
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name    = "${var.project_name}-vpc"
    Project = var.project_name
  }
}

# Internet Gateway: salida bidireccional a Internet
resource "aws_internet_gateway" "main" {
  vpc_id = aws_vpc.main.id

  tags = {
    Name    = "${var.project_name}-igw"
    Project = var.project_name
  }
}

# Elastic IPs para los NAT Gateways (uno por AZ)
resource "aws_eip" "nat" {
  count  = length(var.availability_zones)
  domain = "vpc"

  tags = {
    Name    = "${var.project_name}-nat-eip-${count.index + 1}"
    Project = var.project_name
  }

  depends_on = [aws_internet_gateway.main]
}

# NAT Gateways: uno en cada subred publica para que cada AZ sea autosuficiente
resource "aws_nat_gateway" "main" {
  count         = length(var.availability_zones)
  allocation_id = aws_eip.nat[count.index].id
  subnet_id     = aws_subnet.public[count.index].id

  tags = {
    Name    = "${var.project_name}-nat-${count.index + 1}"
    Project = var.project_name
  }

  depends_on = [aws_internet_gateway.main]
}