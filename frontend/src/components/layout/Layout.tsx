import React, { useState, useEffect } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { motion, AnimatePresence } from 'framer-motion'
import { Menu, X, Phone, Mail, MapPin, ChevronUp } from 'lucide-react'

interface LayoutProps {
  children: React.ReactNode
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const [showBackToTop, setShowBackToTop] = useState(false)
  const location = useLocation()

  const navigation = [
    { name: '首页', path: '/' },
    { name: '关于我们', path: '/about' },
    { name: '服务项目', path: '/services' },
    { name: '案例展示', path: '/cases' },
    { name: '新闻动态', path: '/news' },
    { name: '联系我们', path: '/contact' },
  ]

  // 监听滚动显示返回顶部按钮
  useEffect(() => {
    const handleScroll = () => {
      setShowBackToTop(window.scrollY > 300)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  // 路由变化时关闭移动端菜单
  useEffect(() => {
    setIsMenuOpen(false)
  }, [location])

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  return (
    <div className="min-h-screen bg-white">
      {/* Header */}
      <header className="bg-white shadow-sm sticky top-0 z-50">
        {/* 顶部联系栏 */}
        <div className="bg-blue-600 text-white py-2">
          <div className="container mx-auto px-4 flex justify-between items-center text-sm">
            <div className="flex items-center space-x-6">
              <div className="flex items-center space-x-2">
                <Phone className="w-4 h-4" />
                <span>021-12345678</span>
              </div>
              <div className="flex items-center space-x-2">
                <Mail className="w-4 h-4" />
                <span>info@kuowen-exhibition.com</span>
              </div>
              <div className="flex items-center space-x-2">
                <MapPin className="w-4 h-4" />
                <span>上海市浦东新区</span>
              </div>
            </div>
            <div className="hidden md:block">
              <span>专业展台设计搭建服务</span>
            </div>
          </div>
        </div>

        {/* 主导航 */}
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center py-4">
            {/* Logo */}
            <Link to="/" className="flex items-center space-x-3">
              <div className="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-lg">阔</span>
              </div>
              <div>
                <h1 className="text-xl font-bold text-gray-900">阔文展览</h1>
                <p className="text-sm text-gray-600">专业展台设计搭建</p>
              </div>
            </Link>

            {/* Desktop Navigation */}
            <nav className="hidden md:flex space-x-8">
              {navigation.map((item) => (
                <Link
                  key={item.path}
                  to={item.path}
                  className={`relative px-3 py-2 text-sm font-medium transition-colors duration-200 ${
                    location.pathname === item.path
                      ? 'text-blue-600'
                      : 'text-gray-700 hover:text-blue-600'
                  }`}
                >
                  {item.name}
                  {location.pathname === item.path && (
                    <motion.div
                      className="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600"
                      layoutId="navbar-indicator"
                    />
                  )}
                </Link>
              ))}
            </nav>

            {/* CTA Button */}
            <div className="hidden md:block">
              <Link
                to="/contact"
                className="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors duration-200"
              >
                免费咨询
              </Link>
            </div>

            {/* Mobile menu button */}
            <button
              className="md:hidden p-2"
              onClick={() => setIsMenuOpen(!isMenuOpen)}
            >
              {isMenuOpen ? (
                <X className="w-6 h-6 text-gray-600" />
              ) : (
                <Menu className="w-6 h-6 text-gray-600" />
              )}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        <AnimatePresence>
          {isMenuOpen && (
            <motion.div
              initial={{ opacity: 0, height: 0 }}
              animate={{ opacity: 1, height: 'auto' }}
              exit={{ opacity: 0, height: 0 }}
              className="md:hidden border-t bg-white"
            >
              <nav className="container mx-auto px-4 py-4">
                {navigation.map((item) => (
                  <Link
                    key={item.path}
                    to={item.path}
                    className={`block py-3 px-2 text-base font-medium border-b border-gray-100 ${
                      location.pathname === item.path
                        ? 'text-blue-600'
                        : 'text-gray-700'
                    }`}
                  >
                    {item.name}
                  </Link>
                ))}
                <div className="pt-4">
                  <Link
                    to="/contact"
                    className="block bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg text-center transition-colors duration-200"
                  >
                    免费咨询
                  </Link>
                </div>
              </nav>
            </motion.div>
          )}
        </AnimatePresence>
      </header>

      {/* Main Content */}
      <main className="flex-1">
        {children}
      </main>

      {/* Footer */}
      <footer className="bg-gray-900 text-white">
        <div className="container mx-auto px-4 py-12">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {/* 公司信息 */}
            <div>
              <div className="flex items-center space-x-3 mb-4">
                <div className="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                  <span className="text-white font-bold">阔</span>
                </div>
                <span className="text-xl font-bold">阔文展览</span>
              </div>
              <p className="text-gray-400 mb-4">
                专业的展台设计搭建服务商，致力于为客户提供一站式展览解决方案。
              </p>
              <div className="flex space-x-4">
                <a href="#" className="text-gray-400 hover:text-white transition-colors">
                  微信
                </a>
                <a href="#" className="text-gray-400 hover:text-white transition-colors">
                  微博
                </a>
              </div>
            </div>

            {/* 服务项目 */}
            <div>
              <h3 className="text-lg font-semibold mb-4">服务项目</h3>
              <ul className="space-y-2 text-gray-400">
                <li><a href="/services" className="hover:text-white transition-colors">展台设计</a></li>
                <li><a href="/services" className="hover:text-white transition-colors">展台搭建</a></li>
                <li><a href="/services" className="hover:text-white transition-colors">展会策划</a></li>
                <li><a href="/services" className="hover:text-white transition-colors">设备租赁</a></li>
              </ul>
            </div>

            {/* 快速链接 */}
            <div>
              <h3 className="text-lg font-semibold mb-4">快速链接</h3>
              <ul className="space-y-2 text-gray-400">
                <li><Link to="/about" className="hover:text-white transition-colors">关于我们</Link></li>
                <li><Link to="/cases" className="hover:text-white transition-colors">案例展示</Link></li>
                <li><Link to="/news" className="hover:text-white transition-colors">新闻动态</Link></li>
                <li><Link to="/contact" className="hover:text-white transition-colors">联系我们</Link></li>
              </ul>
            </div>

            {/* 联系方式 */}
            <div>
              <h3 className="text-lg font-semibold mb-4">联系方式</h3>
              <div className="space-y-3 text-gray-400">
                <div className="flex items-start space-x-2">
                  <MapPin className="w-5 h-5 mt-1 flex-shrink-0" />
                  <span>上海市浦东新区张江高科技园区</span>
                </div>
                <div className="flex items-center space-x-2">
                  <Phone className="w-5 h-5" />
                  <span>021-12345678</span>
                </div>
                <div className="flex items-center space-x-2">
                  <Mail className="w-5 h-5" />
                  <span>info@kuowen-exhibition.com</span>
                </div>
              </div>
            </div>
          </div>

          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2025 上海阔文展览展示服务有限公司. 保留所有权利.</p>
          </div>
        </div>
      </footer>

      {/* Back to Top Button */}
      <AnimatePresence>
        {showBackToTop && (
          <motion.button
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            onClick={scrollToTop}
            className="fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-colors duration-200 z-50"
          >
            <ChevronUp className="w-6 h-6" />
          </motion.button>
        )}
      </AnimatePresence>
    </div>
  )
}

export default Layout
